<?php

namespace App\Http\Controllers;

use App\Http\Requests\InterviewRequest;
use App\Http\Requests\InterviewStoreRequest;
use App\Mail\InterviewStored;
use App\Mail\InterviewInvitation;
use App\Models\Interview;
use App\Models\Review;
use App\Models\Statement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;

class InterviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(InterviewStoreRequest $request)
    {
        //
        // force statements check
        $data = $request->all(); // Hämtar all data från requesten (från model/interview.php protected fillable)
        if (count($data['statements']) == 0) {
            return response('At least one statement is required!', 500);
        }
        // set reviewStatusId for review update\
        // plan dependant parameters
        // emails
        $emails = null;
        // review status
        $rsi = 5;
        switch ($data['plan_id']) {
                // interview
            case 1:
                $rsi = 5;
                $emails = null;
                break;
                // webform
            case 3:
                $rsi = 4;
                $emails = 1;
                break;
            default:
                $rsi = 5;
                $emails = null;
                break;
        }
        try {
            DB::transaction(function () use ($data, $rsi, $emails) {
                //Sätter skaparen av interview (aka vilken auditor)
                $data['creator_id'] = auth()->user()->id;
                $data['emails'] = $emails;
                //Skapar interviewrad i databasen
                $interview = Interview::create($data);
                //Lägger till alla statements till interview i interview_statement
                $interview->statements()->attach($data['statements']);
                // update statements
                // Get the organization ID and user ID of the authenticated user
                $organisationId = auth()->user()->organisation_id;
                $userId = auth()->user()->id;
                // Loop through the statements and check for matching reviews
                foreach ($data['statements'] as $statementId) {
                    $review = Review::where('statement_id', $statementId)
                        ->where('organisation_id', $organisationId)
                        ->first();
                    //Om det finns en review, sätt status till aktuell ( pending)
                    if ($review) {
                        $review->review_status_id = $rsi;
                        // Updating review status
                        $review->save(); // Save the update
                    //Annars skapa en ny review Status
                    } else {
                        // Create a new review with status id of 5 (Pending Review)
                        Review::create([
                            'organisation_id' => $organisationId,
                            'statement_id' => $statementId,
                            'user_id' => $userId,
                            'review_status_id' => $rsi,
                            'review' => __('messages.pendingInterview'),
                        ]);
                    }
                }
                // send email
                // if interview (plan_id = 1)
                if ($interview->plan_id == 1) {
                    $creator = auth()->user();
                    $organisation = $creator->organisation;
                    $locale = app()->getLocale();

                    // Get all statements with their content
                    $statements = [];
                    foreach ($data['statements'] as $statementId) {
                        $statement = Statement::find($statementId);
                        if ($statement) {
                            $statements[] = [
                                'content_' . $locale => $statement->{'content_' . $locale},
                            ];
                        }
                    }

                    // Determine recipient email
                    $recipientEmail = $interview->interviewee;
                    if (env('APP_ENV') == 'local') {
                        $recipientEmail = env('MAIL_TEST_ADDRESS', 'janosaudron13@gmail.com');
                    }

                    // Send invitation email
                    Mail::to($recipientEmail)->send(
                        new InterviewInvitation($interview, $creator, $statements, $locale, $organisation)
                    );
                }

                // if webform
                if ($interview->plan_id == 3) {
                    $user = User::where('id', $interview->interviewee)->first();
                    if (env('APP_ENV') == 'local') {
                        $userEmail = 'janosaudron13@gmail.com';
                    }
                    if (env('APP_ENV') == 'production') {
                        $userEmail = $user->email;
                        if ($userEmail == null) {
                            $userEmail = 'fredrik@itsakerhetsbolaget.se';
                        }
                    }
                    $body = __('messages.webformPreview');
                    Mail::to($userEmail)->send(new InterviewStored($user, $body));
                };
                /*
                $userEmail = User::where('id', $data['user_id'])->first();
                $userEmail = $userEmail->email;
                if ($userEmail == null) {
                    $userEmail = 'fredrik@itsakerhetsbolaget.se';
                }
                // Mail Data
                $user = User::where('id', $data['user_id'])->first();
                $creator = auth()->user();
                $statements = [];
                foreach ($data['statements'] as $statementId) {
                    $details = Statement::where('id', $statementId)->first();
                    $statements[] = $details->{'content_' . $data['locale']};
                }
                Mail::to($userEmail)->send(new InterviewStored($user, $creator, $statements, $data['agenda']));
                */
            });
        } catch (\Throwable $th) {
            throw $th;
        }
        return response('success', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    public function show(Interview $interview)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    public function edit(Interview $interview)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Interview $interview)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Interview  $interview
     * @return \Illuminate\Http\Response
     */
    public function destroy(Interview $interview)
    {
        //
    }

    /**
     * Update the status of an interview.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:planned,in_progress,completed,cancelled',
        ]);

        $interview = Interview::findOrFail($id);
        $interview->status = $request->status;

        // Auto-set conducted_date when marking as completed
        if ($request->status === Interview::STATUS_COMPLETED && !$interview->conducted_date) {
            $interview->conducted_date = now();
        }

        $interview->save();

        return response()->json([
            'success' => true,
            'message' => __('messages.statusUpdated'),
            'interview' => $interview
        ]);
    }

    /**
     * Upload a file attachment to an interview.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        try {
            $interview = Interview::findOrFail($id);
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('interviews', $filename, 'public');

            // Get existing attachments or initialize empty array
            $attachments = $interview->attachments ? json_decode($interview->attachments, true) : [];

            // Add new attachment
            $attachments[] = [
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'uploaded_at' => now()->toDateTimeString(),
                'uploaded_by' => auth()->user()->id
            ];

            // Save attachments as JSON
            $interview->attachments = json_encode($attachments);
            $interview->save();

            return response()->json([
                'success' => true,
                'message' => __('messages.fileUploaded'),
                'attachments' => $attachments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.fileUploadFailed')
            ], 500);
        }
    }

    /**
     * Update interview notes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateNotes(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $interview = Interview::findOrFail($id);
        $interview->notes = $request->notes;
        $interview->save();

        return response()->json([
            'success' => true,
            'message' => __('messages.notesSaved'),
            'interview' => $interview
        ]);
    }
}
