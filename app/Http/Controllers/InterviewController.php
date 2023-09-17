<?php

namespace App\Http\Controllers;

use App\Http\Requests\InterviewRequest;
use App\Http\Requests\InterviewStoreRequest;
use App\Mail\InterviewStored;
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
        $data = $request->all();
        if(count($data['statements']) == 0) {
            return response('At least one statement is required!', 500);
        }
        // set reviewStatusId for review update\
        $rsi = 5;
        switch ($data['plan_id']) {
            // interview
            case 1:
                $rsi = 5;
                break;
            // webform
            case 3:
                $rsi = 4;
            default:
                $rsi = 5;
                break;
        }
        try {
            DB::transaction(function () use ($data, $rsi) {
                $data['creator_id'] = auth()->user()->id;
                $interview = Interview::create($data);
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
                    if ($review) {
                            $review->review_status_id = $rsi;
                         // Updating review status
                        $review->save(); // Save the update
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
}
