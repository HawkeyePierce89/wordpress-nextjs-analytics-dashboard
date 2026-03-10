'use client';

import { CardSkeleton } from '@/components/skeleton';
import { ErrorState } from '@/components/error-state';
import { useOverview } from '@/lib/queries';
import { OverviewKpis } from '@/features/dashboard/overview-kpis';
import { PostsByMonthChart } from '@/features/dashboard/posts-by-month-chart';
import { PostsByCategoryChart } from '@/features/dashboard/posts-by-category-chart';
import { TopPostsTable } from '@/features/dashboard/top-posts-table';
import { RecentActivity } from '@/features/dashboard/recent-activity';

export default function DashboardPage() {
  const { data, isLoading, isError, refetch } = useOverview();

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-white">Dashboard</h1>
        <p className="text-sm text-gray-400 mt-1">Content performance overview</p>
      </div>

      {isLoading && (
        <>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            {Array.from({ length: 4 }).map((_, i) => (
              <CardSkeleton key={i} />
            ))}
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {Array.from({ length: 3 }).map((_, i) => (
              <CardSkeleton key={i} />
            ))}
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <CardSkeleton />
            <CardSkeleton />
          </div>
        </>
      )}

      {isError && (
        <ErrorState
          message="Failed to load dashboard data."
          onRetry={() => refetch()}
        />
      )}

      {data && (
        <>
          <OverviewKpis data={data} />

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <PostsByMonthChart postsPerMonth={data.postsPerMonth} />
            <PostsByCategoryChart postsByCategory={data.postsByCategory} />
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <TopPostsTable topPosts={data.topPosts} />
            <RecentActivity events={data.recentActivity} />
          </div>
        </>
      )}
    </div>
  );
}
