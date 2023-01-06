<?php

namespace App\Http\Controllers;

use App\Http\Requests\SanctionUpdateRequest;
use App\Models\Article;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Dpa;
use App\Models\IssueCategory;
use App\Models\Outcome;
use App\Models\Sanction;
use App\Models\Sni;
use App\Models\Statement;
use App\Models\Tag;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class SanctionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dpas = Dpa::select(['dpas.id', 'dpas.title'])
            ->selectRaw('count(*) AS count')
            ->join('sanctions', 'dpas.id', '=', 'sanctions.dpa_id')
            ->groupBy(['dpas.id', 'dpas.title'])
            ->orderBy('title')->get();

        $dpas = $dpas->map(function ($dpa) {
            $dpa->title = str_replace('Category:', '', $dpa->title);
            return $dpa;
        });

        $dpas->makeVisible(['count']);
        $snis = Sni::select(['id', 'code', 'desc_en', 'desc_se'])->orderBy('code')->get();
        $statements = Statement::all()->makeVisible(['subcode'])->sortBy('subcode', SORT_NATURAL);
        $types = Type::select(['id', 'text_en', 'text_se'])->orderBy('text_' . App::currentLocale())->get();

        $messages = __('messages');

        return view('models.sanctions.index', compact('dpas', 'snis', 'statements', 'types', 'messages'));
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Sanction $sanction
     * @return \Illuminate\Http\Response
     */
    public function show(Sanction $sanction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Sanction $sanction
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, Sanction $sanction)
    {
        //
        $articles = Article::all()->sortBy('title');
        $countries = Country::all()->sortBy('name');
        $currencies = Currency::all();
        $sanction->load('sni')->makeVisible(['sni']);
        $sanctionArticlesIds = $sanction->articles->pluck('id')->all();
        $snis = Sni::all()->sortBy('code');
        $types = Type::all()->sortBy('text_' . App::currentLocale());
        $outcomes = Outcome::all()->sortBy('desc_' . App::currentLocale());
        $issueCategories = IssueCategory::all()->sortBy('desc_' . App::currentLocale());
        $tags = Tag::all()->sortBy('tag_' . App::currentLocale());
        $tagIds = $sanction->tags->pluck('id')->all();
        $statements = Statement::all()->sortBy('subcode', SORT_NATURAL);
        $statementIds = $sanction->statements->pluck('id')->all();

        return view('models.sanctions.edit', compact('articles', 'sanction', 'sanctionArticlesIds', 'countries', 'currencies', 'snis', 'types', 'outcomes', 'issueCategories', 'tags', 'tagIds', 'statements', 'statementIds'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sanction $sanction
     * @return \Illuminate\Http\Response
     */
    public function update($locale, SanctionUpdateRequest $request, Sanction $sanction)
    {
        //
        $data = $request->validated();
        if (isset($data['articles'])) {
            $articles = $data['articles'];
            unset($data['articles']);
            $sanction->articles()->detach();
            $sanction->articles()->sync($articles);
        }

        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
            $sanction->tags()->sync($tags);
        } else {
            $sanction->tags()->detach();
        }

        if (isset($data['statements'])) {
            $statements = $data['statements'];
            unset($data['statements']);
            $sanction->statements()->sync($statements);
        } else {
            $sanction->statements()->detach();
        }

        $data['user_id'] = auth()->user()->id;
        $sanction->update($data);
        return redirect()->route('sanctions.index', App::currentLocale())->with('success', __('messages.itemUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Sanction $sanction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sanction $sanction)
    {
        //
    }
}
