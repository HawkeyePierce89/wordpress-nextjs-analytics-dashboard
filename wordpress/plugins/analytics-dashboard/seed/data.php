<?php
/**
 * Seed data definitions for the Analytics Dashboard plugin.
 *
 * All functions return plain arrays; no WordPress functions are called here
 * so the data stays portable and unit-testable.
 */

defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// Authors
// ---------------------------------------------------------------------------

/**
 * Return the 5 demo authors.
 *
 * @return array[]  Each entry: login, email, display_name, dashboard_role.
 */
function ad_seed_authors() {
    return [
        [
            'login'          => 'anna_taylor',
            'email'          => 'anna@example.com',
            'display_name'   => 'Anna Taylor',
            'first_name'     => 'Anna',
            'last_name'      => 'Taylor',
            'dashboard_role' => 'Content Manager',
        ],
        [
            'login'          => 'michael_reed',
            'email'          => 'michael@example.com',
            'display_name'   => 'Michael Reed',
            'first_name'     => 'Michael',
            'last_name'      => 'Reed',
            'dashboard_role' => 'Editor',
        ],
        [
            'login'          => 'sophia_chen',
            'email'          => 'sophia@example.com',
            'display_name'   => 'Sophia Chen',
            'first_name'     => 'Sophia',
            'last_name'      => 'Chen',
            'dashboard_role' => 'SEO Specialist',
        ],
        [
            'login'          => 'daniel_brooks',
            'email'          => 'daniel@example.com',
            'display_name'   => 'Daniel Brooks',
            'first_name'     => 'Daniel',
            'last_name'      => 'Brooks',
            'dashboard_role' => 'Editor',
        ],
        [
            'login'          => 'lisa_martinez',
            'email'          => 'lisa@example.com',
            'display_name'   => 'Lisa Martinez',
            'first_name'     => 'Lisa',
            'last_name'      => 'Martinez',
            'dashboard_role' => 'Content Manager',
        ],
    ];
}

// ---------------------------------------------------------------------------
// Categories
// ---------------------------------------------------------------------------

/**
 * Return the 6 demo category names.
 *
 * @return string[]
 */
function ad_seed_categories() {
    return [
        'SEO',
        'Product Marketing',
        'Engineering',
        'Growth',
        'Case Studies',
        'Company News',
    ];
}

// ---------------------------------------------------------------------------
// Posts
// ---------------------------------------------------------------------------

/**
 * Return the 40 demo posts.
 *
 * Keys per entry:
 *   title, category, status (publish|draft|future), reading_time,
 *   views, engagement, avg_time, bounce, is_featured
 *
 * Counts: 30 published, 5 draft, 5 future; 6 featured.
 *
 * @return array[]
 */
function ad_seed_posts() {
    return [
        // ------------------------------------------------------------------ published + featured
        [
            'title'        => 'How We Improved Organic Traffic by 42% in 3 Months',
            'category'     => 'SEO',
            'status'       => 'publish',
            'reading_time' => 8,
            'views'        => 3241,
            'engagement'   => 87,
            'avg_time'     => 312,
            'bounce'       => 34.5,
            'is_featured'  => true,
        ],
        [
            'title'        => 'Launching a New Pricing Page: Lessons Learned',
            'category'     => 'Growth',
            'status'       => 'publish',
            'reading_time' => 5,
            'views'        => 2819,
            'engagement'   => 82,
            'avg_time'     => 248,
            'bounce'       => 41.2,
            'is_featured'  => true,
        ],
        [
            'title'        => 'The Ultimate Guide to Technical SEO Audits',
            'category'     => 'SEO',
            'status'       => 'publish',
            'reading_time' => 12,
            'views'        => 3108,
            'engagement'   => 85,
            'avg_time'     => 480,
            'bounce'       => 29.8,
            'is_featured'  => true,
        ],
        [
            'title'        => 'How Acme Corp Reduced Churn by 30% With Better Onboarding',
            'category'     => 'Case Studies',
            'status'       => 'publish',
            'reading_time' => 9,
            'views'        => 2974,
            'engagement'   => 91,
            'avg_time'     => 362,
            'bounce'       => 26.4,
            'is_featured'  => true,
        ],
        [
            'title'        => 'Engineering a Real-Time Notification System at Scale',
            'category'     => 'Engineering',
            'status'       => 'publish',
            'reading_time' => 11,
            'views'        => 2543,
            'engagement'   => 79,
            'avg_time'     => 420,
            'bounce'       => 38.7,
            'is_featured'  => true,
        ],
        [
            'title'        => 'Product Hunt Launch Playbook: From Preparation to Results',
            'category'     => 'Product Marketing',
            'status'       => 'publish',
            'reading_time' => 7,
            'views'        => 3352,
            'engagement'   => 88,
            'avg_time'     => 290,
            'bounce'       => 31.0,
            'is_featured'  => true,
        ],
        // ------------------------------------------------------------------ published (not featured)
        [
            'title'        => 'On-Page SEO Checklist for Blog Posts',
            'category'     => 'SEO',
            'status'       => 'publish',
            'reading_time' => 6,
            'views'        => 1897,
            'engagement'   => 74,
            'avg_time'     => 225,
            'bounce'       => 45.3,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Understanding Core Web Vitals and Why They Matter',
            'category'     => 'SEO',
            'status'       => 'publish',
            'reading_time' => 7,
            'views'        => 2134,
            'engagement'   => 76,
            'avg_time'     => 268,
            'bounce'       => 42.1,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Building a Content Calendar That Actually Works',
            'category'     => 'Product Marketing',
            'status'       => 'publish',
            'reading_time' => 5,
            'views'        => 1654,
            'engagement'   => 70,
            'avg_time'     => 198,
            'bounce'       => 48.9,
            'is_featured'  => false,
        ],
        [
            'title'        => 'How to Write Product Descriptions That Convert',
            'category'     => 'Product Marketing',
            'status'       => 'publish',
            'reading_time' => 6,
            'views'        => 1823,
            'engagement'   => 73,
            'avg_time'     => 214,
            'bounce'       => 44.7,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Migrating from Monolith to Microservices: A Practical Guide',
            'category'     => 'Engineering',
            'status'       => 'publish',
            'reading_time' => 14,
            'views'        => 2287,
            'engagement'   => 80,
            'avg_time'     => 548,
            'bounce'       => 33.2,
            'is_featured'  => false,
        ],
        [
            'title'        => 'CI/CD Best Practices for Small Engineering Teams',
            'category'     => 'Engineering',
            'status'       => 'publish',
            'reading_time' => 8,
            'views'        => 1742,
            'engagement'   => 72,
            'avg_time'     => 316,
            'bounce'       => 40.5,
            'is_featured'  => false,
        ],
        [
            'title'        => '5 Growth Experiments We Ran in Q3 (And What We Learned)',
            'category'     => 'Growth',
            'status'       => 'publish',
            'reading_time' => 9,
            'views'        => 2058,
            'engagement'   => 77,
            'avg_time'     => 345,
            'bounce'       => 37.8,
            'is_featured'  => false,
        ],
        [
            'title'        => 'A/B Testing Your Email Subject Lines: A Practical Framework',
            'category'     => 'Growth',
            'status'       => 'publish',
            'reading_time' => 6,
            'views'        => 1589,
            'engagement'   => 68,
            'avg_time'     => 232,
            'bounce'       => 50.1,
            'is_featured'  => false,
        ],
        [
            'title'        => 'From 0 to 10k MRR: How BrightPath Hit Their First Milestone',
            'category'     => 'Case Studies',
            'status'       => 'publish',
            'reading_time' => 10,
            'views'        => 2396,
            'engagement'   => 83,
            'avg_time'     => 394,
            'bounce'       => 30.6,
            'is_featured'  => false,
        ],
        [
            'title'        => 'How DataSync Scaled Their Support Team Without Adding Headcount',
            'category'     => 'Case Studies',
            'status'       => 'publish',
            'reading_time' => 8,
            'views'        => 1934,
            'engagement'   => 78,
            'avg_time'     => 310,
            'bounce'       => 36.4,
            'is_featured'  => false,
        ],
        [
            'title'        => 'We Crossed 1,000 Customers — Here is What We Learned',
            'category'     => 'Company News',
            'status'       => 'publish',
            'reading_time' => 5,
            'views'        => 2762,
            'engagement'   => 84,
            'avg_time'     => 195,
            'bounce'       => 32.9,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Introducing Our New Brand Identity',
            'category'     => 'Company News',
            'status'       => 'publish',
            'reading_time' => 4,
            'views'        => 2108,
            'engagement'   => 71,
            'avg_time'     => 158,
            'bounce'       => 43.6,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Keyword Research for SaaS: Finding Topics That Drive Signups',
            'category'     => 'SEO',
            'status'       => 'publish',
            'reading_time' => 9,
            'views'        => 1678,
            'engagement'   => 73,
            'avg_time'     => 354,
            'bounce'       => 46.2,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Link Building Strategies That Still Work in 2024',
            'category'     => 'SEO',
            'status'       => 'publish',
            'reading_time' => 10,
            'views'        => 1945,
            'engagement'   => 75,
            'avg_time'     => 398,
            'bounce'       => 41.8,
            'is_featured'  => false,
        ],
        [
            'title'        => 'How to Position Your Product in a Crowded Market',
            'category'     => 'Product Marketing',
            'status'       => 'publish',
            'reading_time' => 7,
            'views'        => 1502,
            'engagement'   => 69,
            'avg_time'     => 278,
            'bounce'       => 49.3,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Writing a Compelling Value Proposition From Scratch',
            'category'     => 'Product Marketing',
            'status'       => 'publish',
            'reading_time' => 6,
            'views'        => 1367,
            'engagement'   => 67,
            'avg_time'     => 241,
            'bounce'       => 52.4,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Database Query Optimisation for Web Applications',
            'category'     => 'Engineering',
            'status'       => 'publish',
            'reading_time' => 11,
            'views'        => 1813,
            'engagement'   => 76,
            'avg_time'     => 438,
            'bounce'       => 37.1,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Designing an Accessible UI Component Library',
            'category'     => 'Engineering',
            'status'       => 'publish',
            'reading_time' => 9,
            'views'        => 1246,
            'engagement'   => 65,
            'avg_time'     => 358,
            'bounce'       => 54.8,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Viral Loops 101: Building Growth Into Your Product',
            'category'     => 'Growth',
            'status'       => 'publish',
            'reading_time' => 7,
            'views'        => 1729,
            'engagement'   => 72,
            'avg_time'     => 278,
            'bounce'       => 44.0,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Referral Programs That Drive Real Revenue',
            'category'     => 'Growth',
            'status'       => 'publish',
            'reading_time' => 6,
            'views'        => 1384,
            'engagement'   => 66,
            'avg_time'     => 238,
            'bounce'       => 51.7,
            'is_featured'  => false,
        ],
        [
            'title'        => 'How TechFlow Cut Deployment Time From Hours to Minutes',
            'category'     => 'Case Studies',
            'status'       => 'publish',
            'reading_time' => 7,
            'views'        => 1591,
            'engagement'   => 71,
            'avg_time'     => 285,
            'bounce'       => 43.1,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Year in Review: Our Biggest Wins and Lessons of 2023',
            'category'     => 'Company News',
            'status'       => 'publish',
            'reading_time' => 6,
            'views'        => 2341,
            'engagement'   => 80,
            'avg_time'     => 234,
            'bounce'       => 35.5,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Meet the Team: Engineering Edition',
            'category'     => 'Company News',
            'status'       => 'publish',
            'reading_time' => 4,
            'views'        => 1187,
            'engagement'   => 63,
            'avg_time'     => 158,
            'bounce'       => 56.2,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Structured Data and Schema Markup: A Beginner\'s Guide',
            'category'     => 'SEO',
            'status'       => 'publish',
            'reading_time' => 8,
            'views'        => 1436,
            'engagement'   => 69,
            'avg_time'     => 320,
            'bounce'       => 47.6,
            'is_featured'  => false,
        ],
        // ------------------------------------------------------------------ draft (5)
        [
            'title'        => 'Q1 Content Strategy and Planning',
            'category'     => 'Product Marketing',
            'status'       => 'draft',
            'reading_time' => 5,
            'views'        => 0,
            'engagement'   => 0,
            'avg_time'     => 0,
            'bounce'       => 0.0,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Internal SEO Audit Findings — March 2024',
            'category'     => 'SEO',
            'status'       => 'draft',
            'reading_time' => 7,
            'views'        => 0,
            'engagement'   => 0,
            'avg_time'     => 0,
            'bounce'       => 0.0,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Engineering Principles and Coding Standards',
            'category'     => 'Engineering',
            'status'       => 'draft',
            'reading_time' => 10,
            'views'        => 0,
            'engagement'   => 0,
            'avg_time'     => 0,
            'bounce'       => 0.0,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Partnership Announcement Draft',
            'category'     => 'Company News',
            'status'       => 'draft',
            'reading_time' => 3,
            'views'        => 0,
            'engagement'   => 0,
            'avg_time'     => 0,
            'bounce'       => 0.0,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Growth Hacking Myths We Need to Stop Believing',
            'category'     => 'Growth',
            'status'       => 'draft',
            'reading_time' => 6,
            'views'        => 0,
            'engagement'   => 0,
            'avg_time'     => 0,
            'bounce'       => 0.0,
            'is_featured'  => false,
        ],
        // ------------------------------------------------------------------ future/scheduled (5)
        [
            'title'        => 'Building a Better Content Workflow for a Distributed Team',
            'category'     => 'Engineering',
            'status'       => 'future',
            'reading_time' => 8,
            'views'        => 0,
            'engagement'   => 0,
            'avg_time'     => 0,
            'bounce'       => 0.0,
            'is_featured'  => false,
        ],
        [
            'title'        => 'How to Scale Your SEO Program Beyond a Single Writer',
            'category'     => 'SEO',
            'status'       => 'future',
            'reading_time' => 9,
            'views'        => 0,
            'engagement'   => 0,
            'avg_time'     => 0,
            'bounce'       => 0.0,
            'is_featured'  => false,
        ],
        [
            'title'        => 'The Next Chapter: What\'s Coming in H2 2024',
            'category'     => 'Company News',
            'status'       => 'future',
            'reading_time' => 4,
            'views'        => 0,
            'engagement'   => 0,
            'avg_time'     => 0,
            'bounce'       => 0.0,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Conversion Rate Optimisation: Quick Wins for Landing Pages',
            'category'     => 'Growth',
            'status'       => 'future',
            'reading_time' => 7,
            'views'        => 0,
            'engagement'   => 0,
            'avg_time'     => 0,
            'bounce'       => 0.0,
            'is_featured'  => false,
        ],
        [
            'title'        => 'Customer Success Stories: Volume 3',
            'category'     => 'Case Studies',
            'status'       => 'future',
            'reading_time' => 8,
            'views'        => 0,
            'engagement'   => 0,
            'avg_time'     => 0,
            'bounce'       => 0.0,
            'is_featured'  => false,
        ],
    ];
}

// ---------------------------------------------------------------------------
// Excerpts
// ---------------------------------------------------------------------------

/**
 * Return 10 realistic excerpt strings (cycled across the 40 posts).
 *
 * @return string[]
 */
function ad_seed_excerpts() {
    return [
        'A deep dive into the tactics, tools, and experiments that moved the needle on our organic search performance.',
        'We redesigned our pricing page three times before it stuck. Here is everything we tried and what finally worked.',
        'From crawl budget management to Core Web Vitals, this guide covers every layer of a modern technical SEO audit.',
        'A behind-the-scenes look at how one of our customers transformed their onboarding flow and cut churn significantly.',
        'Scalability does not happen by accident. We break down the architecture decisions behind our notification pipeline.',
        'A step-by-step playbook drawn from our own Product Hunt launch — including the mistakes we would rather you avoid.',
        'Content calendars fail when they live in spreadsheets and die in Slack. We rebuilt ours from the ground up.',
        'Positioning is not about features — it is about the story your best customers tell themselves. Here is our framework.',
        'Slow queries are the silent killer of web application performance. Learn the patterns we use to fix them fast.',
        'Referral programs that feel transactional go ignored. We share the psychology and mechanics behind ones that work.',
    ];
}

// ---------------------------------------------------------------------------
// Content block
// ---------------------------------------------------------------------------

/**
 * Return a reusable ~300-word HTML content block used as post body.
 *
 * @return string
 */
function ad_seed_content_block() {
    return <<<HTML
<h2>Why This Matters</h2>
<p>In a landscape where content teams are expected to do more with less, having clear data on what is working — and what is not — is no longer optional. The decisions that drive the biggest results are rarely the loudest ones; they are the consistent, informed choices made week after week.</p>

<h3>The Challenge We Set Out to Solve</h3>
<p>Most analytics dashboards are built for marketers who already know what they are looking for. They assume you have a hypothesis, a campaign, and a comparison period in mind. Our approach is different: we start with the content itself and surface the signals that editorial teams actually care about.</p>
<ul>
    <li>Which posts are driving qualified traffic, not just raw page views?</li>
    <li>Where is engagement dropping off, and does that correlate with reading time?</li>
    <li>How do SEO completeness and bounce rate interact across categories?</li>
</ul>

<h3>What the Data Shows</h3>
<p>After analysing several months of publishing data, a few patterns emerge repeatedly. Posts above 2,500 views tend to share three characteristics: a clear, specific headline; a reading time between six and ten minutes; and an SEO description that mirrors the search intent of the target keyword. Posts that are missing any one of these elements show materially higher bounce rates.</p>

<h3>Putting It Into Practice</h3>
<p>The goal is not to turn every writer into an analyst. It is to give editorial teams a shared vocabulary around performance — one that makes standups shorter, feedback more specific, and planning cycles more confident.</p>
<p>Start small: pick one metric to track consistently for a quarter, build a habit around reviewing it, then layer in the next. Data-informed content strategy is a compounding skill, and the teams that invest in it early tend to outperform significantly over a twelve-month horizon.</p>
HTML;
}

// ---------------------------------------------------------------------------
// Activity events
// ---------------------------------------------------------------------------

/**
 * Return 18 seed activity events.
 *
 * Each entry has: type, user, message.
 * The post_id will be assigned dynamically in seed.php.
 *
 * @return array[]
 */
function ad_seed_activity_events() {
    return [
        [
            'type'    => 'published',
            'user'    => 'Anna Taylor',
            'message' => 'Published "How We Improved Organic Traffic by 42% in 3 Months".',
        ],
        [
            'type'    => 'seo_updated',
            'user'    => 'Sophia Chen',
            'message' => 'Updated SEO title and description for the organic traffic post.',
        ],
        [
            'type'    => 'featured_changed',
            'user'    => 'Anna Taylor',
            'message' => 'Marked "How We Improved Organic Traffic by 42% in 3 Months" as featured.',
        ],
        [
            'type'    => 'published',
            'user'    => 'Michael Reed',
            'message' => 'Published "Launching a New Pricing Page: Lessons Learned".',
        ],
        [
            'type'    => 'updated',
            'user'    => 'Michael Reed',
            'message' => 'Revised introduction section of the pricing page post.',
        ],
        [
            'type'    => 'featured_changed',
            'user'    => 'Anna Taylor',
            'message' => 'Marked "Launching a New Pricing Page: Lessons Learned" as featured.',
        ],
        [
            'type'    => 'published',
            'user'    => 'Sophia Chen',
            'message' => 'Published "The Ultimate Guide to Technical SEO Audits".',
        ],
        [
            'type'    => 'seo_updated',
            'user'    => 'Sophia Chen',
            'message' => 'Added structured data recommendations to the SEO audit guide.',
        ],
        [
            'type'    => 'published',
            'user'    => 'Daniel Brooks',
            'message' => 'Published "How Acme Corp Reduced Churn by 30% With Better Onboarding".',
        ],
        [
            'type'    => 'updated',
            'user'    => 'Daniel Brooks',
            'message' => 'Added customer quote and updated metrics in the Acme Corp case study.',
        ],
        [
            'type'    => 'created',
            'user'    => 'Lisa Martinez',
            'message' => 'Created draft "Q1 Content Strategy and Planning".',
        ],
        [
            'type'    => 'published',
            'user'    => 'Lisa Martinez',
            'message' => 'Published "We Crossed 1,000 Customers — Here is What We Learned".',
        ],
        [
            'type'    => 'featured_changed',
            'user'    => 'Anna Taylor',
            'message' => 'Marked "Engineering a Real-Time Notification System at Scale" as featured.',
        ],
        [
            'type'    => 'seo_updated',
            'user'    => 'Sophia Chen',
            'message' => 'Refreshed meta description for "On-Page SEO Checklist for Blog Posts".',
        ],
        [
            'type'    => 'created',
            'user'    => 'Michael Reed',
            'message' => 'Created draft "Internal SEO Audit Findings — March 2024".',
        ],
        [
            'type'    => 'published',
            'user'    => 'Daniel Brooks',
            'message' => 'Published "5 Growth Experiments We Ran in Q3 (And What We Learned)".',
        ],
        [
            'type'    => 'updated',
            'user'    => 'Sophia Chen',
            'message' => 'Updated reading time estimate for the keyword research post.',
        ],
        [
            'type'    => 'created',
            'user'    => 'Lisa Martinez',
            'message' => 'Scheduled "Building a Better Content Workflow for a Distributed Team" for next week.',
        ],
    ];
}
