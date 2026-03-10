'use client';

import { useAnalytics } from '@/lib/queries';
import { CardSkeleton } from '@/components/skeleton';
import { ErrorState } from '@/components/error-state';
import { PublishingTrendChart } from '@/features/analytics/publishing-trend-chart';
import { DraftVsPublishedChart } from '@/features/analytics/draft-vs-published-chart';
import { TopCategoriesChart } from '@/features/analytics/top-categories-chart';
import { TopAuthorsChart } from '@/features/analytics/top-authors-chart';
import { ReadingTimeByCategoryChart } from '@/features/analytics/reading-time-by-category-chart';
import { ContentHealthSummary } from '@/features/analytics/content-health-summary';
import { TopPostsByViews } from '@/features/analytics/top-posts-by-views';

export default function AnalyticsPage() {
  const { data, isLoading, isError, refetch } = useAnalytics();

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-white">Analytics</h1>
        <p className="text-sm text-gray-400 mt-1">Content insights and performance</p>
      </div>

      {isLoading && (
        <>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <CardSkeleton />
            <CardSkeleton />
          </div>
          <CardSkeleton />
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <CardSkeleton />
            <CardSkeleton />
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <CardSkeleton />
            <CardSkeleton />
          </div>
        </>
      )}

      {isError && (
        <ErrorState
          message="Failed to load analytics data."
          onRetry={() => refetch()}
        />
      )}

      {data && (
        <>
          {/* 2-col: Publishing Trend + Draft vs Published */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <PublishingTrendChart postsPerMonth={data.postsPerMonth} />
            <DraftVsPublishedChart draftVsPublished={data.draftVsPublished} />
          </div>

          {/* Full-width: Reading Time by Category */}
          <ReadingTimeByCategoryChart
            avgReadingTimeByCategory={data.avgReadingTimeByCategory}
          />

          {/* 2-col: Top Categories + Top Authors */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <TopCategoriesChart topCategories={data.topCategories} />
            <TopAuthorsChart topAuthors={data.topAuthors} />
          </div>

          {/* 2-col: Content Health + Top Posts */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <ContentHealthSummary contentHealth={data.contentHealth} />
            <TopPostsByViews topPostsByViews={data.topPostsByViews} />
          </div>
        </>
      )}
    </div>
  );
}
