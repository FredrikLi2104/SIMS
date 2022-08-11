<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\ActionTypeController;
use App\Http\Controllers\AxiosController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\DpaController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\SniController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\StatementTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Root Routing */

Route::get('/', [RoutingController::class, 'root'])->name('root');
Route::post('/theme-switch', [RoutingController::class, 'themeSwitcher'])->name('theme.switch');
//Route::post('/set-session', [RoutingController::class, 'setSession'])->name('set.session');

/* Axios */
Route::prefix('{locale}/axios')->middleware('auth')->group(function () {
    Route::get('countries', [AxiosController::class, 'countries'])->middleware('can:moderator')->name('axios.countries.index');
    Route::get('dpas', [AxiosController::class, 'dpas'])->middleware('can:moderator')->name('axios.dpas.index');
    Route::get('kpis', [AxiosController::class, 'kpis'])->middleware('can:moderator')->name('axios.kpis.index');
    Route::get('messages', [AxiosController::class, 'messages'])->middleware('can:all')->name('axios.messages');
    Route::get('organisations/do', [AxiosController::class, 'organisationsDo'])->middleware('can:user')->name('axios.organisations.do');
    Route::post('organisations/kpicomments/store', [AxiosController::class, 'organisationsKpicommentsStore'])->middleware('can:user')->name('axios.organisations.kpicomments.store');
    Route::get('organisations/kpis/{kpi}', [AxiosController::class, 'organisationsKpisShow'])->middleware('can:auditor-user')->name('axios.organisations.kpis.show');
    Route::get('organisations/kpis', [AxiosController::class, 'organisationsKpis'])->middleware('can:auditor-user')->name('axios.organisations.kpis');
    Route::get('organisations/plan', [AxiosController::class, 'organisationsPlan'])->middleware('can:user')->name('axios.organisations.plan');
    Route::get('organisations/review', [AxiosController::class, 'organisationsReview'])->middleware('can:auditor')->name('axios.organisations.review');
    Route::post('organisations/components/periods/update', [AxiosController::class, 'organisationsComponentsPeriodsUpdate'])->middleware('can:user')->name('axios.organisations.components.periods.update');
    Route::get('organisations/risks', [AxiosController::class, 'organisationsRisksIndex'])->middleware('can:auditor-user')->name('axios.organisations.risks.index');
    Route::post('organisations/statements/deeds/update', [AxiosController::class, 'organisationsStatementsDeedsUpdate'])->middleware('can:user')->name('axios.organisations.statements.deeds.update');
    Route::post('organisations/statements/deeds/update-all', [AxiosController::class, 'organisationsStatementsDeedsUpdateAll'])->middleware('can:user')->name('axios.organisations.statements.deeds.update-all');
    Route::post('organisations/statements/plans/update', [AxiosController::class, 'organisationsStatementsPlansUpdate'])->middleware('can:user')->name('axios.organisations.statements.plans.update');
    Route::post('organisations/statements/reviews/update', [AxiosController::class, 'organisationsStatementsReviewsUpdate'])->middleware('can:auditor')->name('axios.organisations.statements.reviews.update');
    Route::post('organisations/update', [AxiosController::class, 'organisationsUpdate'])->middleware('can:user')->name('axios.organisations.update');
    Route::post('risk_comments/store', [AxiosController::class, 'riskCommentsStore'])->middleware('can:auditor-user')->name('axios.risk_comments.store');
    Route::get('risks/{risk}', [AxiosController::class, 'risksShow'])->middleware('can:auditor-user')->name('axios.risks.show');
    Route::get('sanctions', [AxiosController::class, 'sanctions'])->middleware('can:moderator')->name('axios.sanctions.index');
});

/* Localized Routes */
Route::prefix('{locale}')->middleware('locale')->group(function () {
    require __DIR__ . '/auth.php';
    Route::get('do', [OrganisationController::class, 'do'])->middleware('auth')->middleware('can:user')->name('organisations.do');
    Route::resource('dpas', DpaController::class)->middleware('auth')->middleware('can:moderator');
    Route::get('/home', [RoutingController::class, 'home'])->middleware('auth')->name('home');
    Route::resource('components', ComponentController::class)->middleware('auth')->middleware('can:moderator');
    Route::resource('kpis', KpiController::class)->middleware('auth')->middleware('can:moderator');
    Route::get('organisations/kpis', [OrganisationController::class, 'kpisIndex'])->middleware('auth')->middleware('can:auditor-user')->name('organisations.kpis');
    Route::resource('organisations', OrganisationController::class)->middleware('auth')->middleware('can:moderator');
    Route::resource('periods', PeriodController::class)->middleware('auth')->middleware('can:moderator');
    Route::get('plan', [OrganisationController::class, 'plan'])->middleware('auth')->middleware('can:user')->name('organisations.plan');
    Route::resource('plans', PlanController::class)->middleware('auth')->middleware('can:moderator');
    Route::get('review', [OrganisationController::class, 'review'])->middleware('auth')->middleware('can:auditor')->name('organisations.review');
    Route::resource('risks', RiskController::class)->middleware('auth')->middleware('can:auditor-user');
    Route::resource('sanctions', SanctionController::class)->middleware('auth')->middleware('can:moderator');
    Route::resource('statements', StatementController::class)->middleware('auth')->middleware('can:moderator');
    Route::resource('statement_types', StatementTypeController::class)->middleware('auth')->middleware('can:moderator');
    Route::resource('snis', SniController::class)->middleware('auth')->middleware('can:moderator');
    Route::resource('users', UserController::class)->middleware('auth')->middleware('can:moderator');
});

/* Services - Do not Modify */
Route::prefix('services')->middleware('auth')->group(function () {
    //Route::get('countries/seed', [RoutingController::class, 'countriesSeed'])->middleware('can:super')->name('countries.seed');
});
