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
use App\Services\CalendarService;
use Carbon\Carbon;

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

                    // Queue invitation email
                    Mail::to($recipientEmail)->queue(
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
                    Mail::to($userEmail)->queue(new InterviewStored($user, $body));
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
    public function updateStatus(Request $request, $locale, $interview_id)
    {
        $request->validate([
            'status' => 'required|in:planned,in_progress,completed,cancelled',
        ]);

        try {
            $interview = Interview::findOrFail($interview_id);
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Interview not found with ID: ' . $interview_id
            ], 404);
        }
    }

    /**
     * Upload a file attachment to an interview.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadFile(Request $request, $locale, $interview_id)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        try {
            $interview = Interview::findOrFail($interview_id);
            $file = $request->file('file');

            $originalName = $file->getClientOriginalName();
            $filename = time() . '_' . $originalName;

            // Store file using Laravel's storage system
            $path = $file->storeAs('interviews', $filename, 'public');

            // Get existing attachments or initialize empty array
            $attachments = $interview->attachments ? json_decode($interview->attachments, true) : [];

            // Add new attachment
            $attachments[] = [
                'filename' => $originalName,
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Interview not found with ID: ' . $interview_id
            ], 404);
        } catch (\Exception $e) {
            \Log::error('File upload failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => __('messages.fileUploadFailed') . ': ' . $e->getMessage()
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
    public function updateNotes(Request $request, $locale, $interview_id)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        try {
            $interview = Interview::findOrFail($interview_id);
            $interview->notes = $request->notes;
            $interview->save();

            return response()->json([
                'success' => true,
                'message' => __('messages.notesSaved'),
                'interview' => $interview
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Interview not found with ID: ' . $interview_id
            ], 404);
        }
    }

    /**
     * Schemalägga intervjumöte och skicka ICS-inbjudan
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale
     * @param  int  $interview_id
     * @return \Illuminate\Http\Response
     */
    public function scheduleInterview(Request $request, $locale, $interview_id)
    {
        $request->validate([
            'scheduled_date' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:480',
        ]);

        try {
            $interview = Interview::findOrFail($interview_id);
            $organizer = auth()->user();

            // Uppdatera interview
            $interview->scheduled_date = Carbon::parse($request->scheduled_date);
            $interview->status = 'planned';
            $interview->save();

            // Generera ICS-fil
            $calendarService = new CalendarService();
            $ics = $calendarService->generateICS($interview, $organizer, [
                'duration' => $request->duration
            ]);

            // Skicka email med ICS-bilaga
            Mail::to($interview->interviewee)
                ->cc($organizer->email)
                ->send(new \App\Mail\InterviewScheduleInvitation($interview, $organizer, $ics, $request->duration));

            return response()->json([
                'success' => true,
                'message' => __('messages.meetingInvitationSent'),
                'interview' => $interview->fresh()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Interview not found with ID: ' . $interview_id
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule interview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Uppdatera schemalagt möte
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale
     * @param  int  $interview_id
     * @return \Illuminate\Http\Response
     */
    public function updateSchedule(Request $request, $locale, $interview_id)
    {
        $request->validate([
            'scheduled_date' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:480',
        ]);

        try {
            $interview = Interview::findOrFail($interview_id);
            $organizer = auth()->user();

            // Uppdatera interview
            $interview->scheduled_date = Carbon::parse($request->scheduled_date);
            $interview->save();

            // Generera uppdaterad ICS-fil (med högre SEQUENCE)
            $calendarService = new CalendarService();
            $ics = $calendarService->generateICS($interview, $organizer, [
                'duration' => $request->duration
            ]);

            // Skicka uppdaterad inbjudan
            Mail::to($interview->interviewee)
                ->cc($organizer->email)
                ->send(new \App\Mail\InterviewScheduleInvitation($interview, $organizer, $ics, $request->duration, 'updated'));

            return response()->json([
                'success' => true,
                'message' => __('messages.meetingUpdated'),
                'interview' => $interview->fresh()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Interview not found with ID: ' . $interview_id
            ], 404);
        }
    }

    /**
     * Avboka schemalagt möte
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale
     * @param  int  $interview_id
     * @return \Illuminate\Http\Response
     */
    public function cancelSchedule(Request $request, $locale, $interview_id)
    {
        try {
            $interview = Interview::findOrFail($interview_id);
            $organizer = auth()->user();

            // Generera avboknings-ICS
            $calendarService = new CalendarService();
            $ics = $calendarService->generateCancellationICS($interview, $organizer);

            // Skicka avbokningsmail
            Mail::to($interview->interviewee)
                ->cc($organizer->email)
                ->send(new \App\Mail\InterviewScheduleInvitation($interview, $organizer, $ics, 0, 'cancelled'));

            // Uppdatera interview
            $interview->scheduled_date = null;
            $interview->status = 'planned';
            $interview->save();

            return response()->json([
                'success' => true,
                'message' => __('messages.meetingCancelled'),
                'interview' => $interview->fresh()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Interview not found with ID: ' . $interview_id
            ], 404);
        }
    }

    /**
     * Download an interview attachment.
     *
     * @param  string  $locale
     * @param  int  $interview_id
     * @param  string  $filename
     * @return \Illuminate\Http\Response
     */
    public function downloadFile($locale, $interview_id, $filename)
    {
        try {
            $interview = Interview::findOrFail($interview_id);

            // Verify the file belongs to this interview
            $attachments = $interview->attachments ? json_decode($interview->attachments, true) : [];
            $found = false;
            $originalName = null;

            foreach ($attachments as $attachment) {
                if (basename($attachment['path']) === $filename) {
                    $found = true;
                    $originalName = $attachment['filename'];
                    break;
                }
            }

            if (!$found) {
                abort(404, 'File not found in interview attachments');
            }

            $path = storage_path('app/public/interviews/' . $filename);

            if (!file_exists($path)) {
                abort(404, 'File not found on disk');
            }

            // Download with original filename
            return response()->download($path, $originalName);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Interview not found');
        }
    }

    /**
     * Delete an interview attachment.
     *
     * @param  string  $locale
     * @param  int  $interview_id
     * @param  string  $filename
     * @return \Illuminate\Http\Response
     */
    public function deleteFile($locale, $interview_id, $filename)
    {
        try {
            $interview = Interview::findOrFail($interview_id);

            // Get existing attachments
            $attachments = $interview->attachments ? json_decode($interview->attachments, true) : [];
            $found = false;
            $newAttachments = [];

            // Remove the attachment from the list
            foreach ($attachments as $attachment) {
                $attachmentFilename = basename($attachment['path']);

                // Compare both the full filename and try matching with the path
                if ($attachmentFilename === $filename || $attachment['path'] === $filename || str_ends_with($attachment['path'], '/' . $filename)) {
                    $found = true;
                    // Delete the physical file
                    $path = storage_path('app/public/' . $attachment['path']);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                } else {
                    $newAttachments[] = $attachment;
                }
            }

            if (!$found) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found in interview attachments. Searched for: ' . $filename
                ], 404);
            }

            // Update interview attachments
            $interview->attachments = count($newAttachments) > 0 ? json_encode($newAttachments) : null;
            $interview->save();

            return response()->json([
                'success' => true,
                'message' => __('messages.fileDeleted'),
                'attachments' => $newAttachments
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Interview not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.fileDeleteFailed') . ': ' . $e->getMessage()
            ], 500);
        }
    }
}
