<?php
/**
 * Test script to verify that Reviews are created correctly from Test Conduct
 * Run with: php test_review_creation.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Review;
use App\Models\ReviewStatus;
use App\Models\Statement;
use Illuminate\Support\Facades\DB;

echo "=== Test Review Creation Verification ===\n\n";

// Get review statuses
echo "1. Review Statuses:\n";
$statuses = ReviewStatus::all();
foreach ($statuses as $status) {
    echo "   ID {$status->id}: {$status->name_en} / {$status->name_se}\n";
}

// Count total reviews
echo "\n2. Total Reviews in database: " . Review::count() . "\n";

// Show latest 5 reviews
echo "\n3. Latest 5 Reviews:\n";
$latestReviews = Review::with(['statement', 'reviewStatus', 'user'])
    ->orderBy('updated_at', 'desc')
    ->limit(5)
    ->get();

if ($latestReviews->isEmpty()) {
    echo "   No reviews found yet.\n";
} else {
    foreach ($latestReviews as $review) {
        echo "\n   Review ID: {$review->id}\n";
        echo "   Statement: {$review->statement->subcode} - " . substr($review->statement->content_en, 0, 50) . "...\n";
        echo "   Status: {$review->reviewStatus->name_en}\n";
        echo "   User: {$review->user->name}\n";
        echo "   Updated: {$review->updated_at}\n";
        echo "   Comment preview: " . substr($review->review, 0, 100) . "...\n";
    }
}

// Check for test-related reviews (reviews created by test system)
echo "\n4. Test-related Reviews (contains 'Test genomfört'):\n";
$testReviews = Review::where('review', 'LIKE', 'Test genomfört%')
    ->with(['statement', 'reviewStatus'])
    ->orderBy('updated_at', 'desc')
    ->limit(5)
    ->get();

if ($testReviews->isEmpty()) {
    echo "   No test-related reviews found yet.\n";
    echo "   (This is normal if you haven't conducted any tests yet)\n";
} else {
    foreach ($testReviews as $review) {
        echo "\n   Review ID: {$review->id}\n";
        echo "   Statement: {$review->statement->subcode}\n";
        echo "   Status: {$review->reviewStatus->name_en}\n";
        echo "   Created: {$review->created_at}\n";
        echo "   Full comment:\n";
        echo "   " . str_replace("\n", "\n   ", $review->review) . "\n";
    }
}

// Check auditor_statement with test data
echo "\n5. Auditor Statements with Test Data:\n";
$testStatements = DB::table('auditor_statement')
    ->where('plan_id', 2) // Test plan
    ->whereNotNull('test_plan')
    ->orderBy('updated_at', 'desc')
    ->limit(5)
    ->get();

if ($testStatements->isEmpty()) {
    echo "   No test plans found yet.\n";
    echo "   Create a test plan first using the 'Prepare' button.\n";
} else {
    foreach ($testStatements as $ts) {
        echo "\n   Statement ID: {$ts->statement_id}\n";
        echo "   Test Method: {$ts->test_method}\n";
        echo "   Test Status: {$ts->test_status}\n";
        echo "   Test Date: " . ($ts->test_date ?? 'Not conducted yet') . "\n";
        echo "   Has Result: " . ($ts->test_result ? 'Yes' : 'No') . "\n";

        // Check if corresponding review exists
        $review = Review::where('statement_id', $ts->statement_id)->first();
        if ($review) {
            echo "   ✓ Review EXISTS (ID: {$review->id}, Status: {$review->reviewStatus->name_en})\n";
        } else {
            echo "   ✗ Review NOT FOUND (conduct test and select outcome to create review)\n";
        }
    }
}

echo "\n=== Summary ===\n";
echo "Total reviews: " . Review::count() . "\n";
echo "Test-related reviews: " . Review::where('review', 'LIKE', 'Test genomfört%')->count() . "\n";
echo "Statements with test plans: " . DB::table('auditor_statement')->where('plan_id', 2)->whereNotNull('test_plan')->count() . "\n";

echo "\n=== How to Test ===\n";
echo "1. Go to Review page\n";
echo "2. Click 'Prepare' on Test card\n";
echo "3. Create a test plan for a statement\n";
echo "4. Click 'Conduct' on Test card\n";
echo "5. Select 'Test Outcome' (Passed/Failed)\n";
echo "6. Add test result and evidence\n";
echo "7. Save\n";
echo "8. Run this script again to see the created review\n";

echo "\n=== Test Complete ===\n";
