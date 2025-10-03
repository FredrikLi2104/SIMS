<?php

namespace App\Http\Controllers;

use App\Http\Requests\FaqStoreRequest;
use App\Http\Requests\FaqUpdateRequest;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['messages'] = __('messages');

        return view('models/faqs/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($locale)
    {
        App::setLocale($locale);

        $sortOrder = Faq::latest('sort_order')->first()?->sort_order;
        $data['sort_order'] = is_null($sortOrder) ? 0 : $sortOrder + 1;
        $data['action_msg'] = __('messages.create');
        $data['title'] = __('messages.faqs') . ' ' . __('messages.create');
        $data['messages'] = __('messages');

        return view('models/faqs/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(FaqStoreRequest $request)
    {
        $data = $request->validated();
        $data['live'] = $request->input('live') ?? false;

        Faq::create($data);

        return ['success' => true];
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Faq $faq
     * @return \Illuminate\Http\Response
     */
    public function show(Faq $faq)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Faq $faq
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Faq $faq)
    {
        App::setLocale($locale);

        $data['is_update'] = true;
        $data['faq'] = $faq;
        $data['sort_order'] = $faq->sort_order;
        $data['action_msg'] = __('messages.edit');
        $data['title'] = __('messages.faqs') . ' ' . __('messages.edit');
        $data['messages'] = __('messages');

        return view('models/faqs/create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Faq $faq
     * @return \Illuminate\Http\Response
     */
    public function update(FaqUpdateRequest $request, $locale, Faq $faq)
    {
        $data = $request->validated();
        $data['live'] = $request->input('live') ?? false;
        $faq->update($data);

        return ['success' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Faq $faq
     * @return \Illuminate\Http\Response
     */
    public function destroy($locale, Faq $faq)
    {
        $result = $faq->delete();
        $data = $result ? ['success' => true, 'msg' => __('messages.delete_success')] : ['error' => true, 'msg' => __('messages.error')];

        return response()->json($data);
    }
}
