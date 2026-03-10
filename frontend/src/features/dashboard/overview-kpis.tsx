'use client';

import { KpiCard } from '@/components/kpi-card';
import type { OverviewResponse } from '@/types';

type OverviewKpisProps = {
  data: OverviewResponse;
};

function formatViews(n: number): string {
  if (n >= 1_000_000) return `${(n / 1_000_000).toFixed(1)}M`;
  if (n >= 1_000) return `${(n / 1_000).toFixed(1)}K`;
  return String(n);
}

function formatReadingTime(minutes: number): string {
  return `${minutes.toFixed(1)}m`;
}

export function OverviewKpis({ data }: OverviewKpisProps) {
  return (
    <div className="space-y-4">
      <div className="grid grid-cols-4 gap-4">
        <KpiCard
          title="Total Posts"
          value={data.totalPosts}
          delta={{ value: '8%', positive: true }}
        />
        <KpiCard
          title="Published"
          value={data.publishedPosts}
          delta={{ value: '12%', positive: true }}
        />
        <KpiCard
          title="Total Views"
          value={formatViews(data.totalViews)}
          delta={{ value: '18%', positive: true }}
        />
        <KpiCard
          title="Avg Engagement"
          value={`${data.avgEngagementScore.toFixed(1)}%`}
          delta={{ value: '3%', positive: false }}
        />
      </div>
      <div className="grid grid-cols-3 gap-4">
        <KpiCard
          title="Drafts"
          value={data.draftPosts}
          delta={{ value: '5%', positive: false }}
        />
        <KpiCard
          title="Authors"
          value={data.totalAuthors}
          delta={{ value: '2', positive: true }}
        />
        <KpiCard
          title="Avg Reading Time"
          value={formatReadingTime(data.avgReadingTime)}
          delta={{ value: '0.3m', positive: true }}
        />
      </div>
    </div>
  );
}
