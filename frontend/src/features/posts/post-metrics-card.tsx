'use client';

import type { PostMetrics } from '@/types';

type PostMetricsCardProps = {
  metrics: PostMetrics;
};

function formatAvgTime(seconds: number): string {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins}:${secs.toString().padStart(2, '0')}`;
}

export function PostMetricsCard({ metrics }: PostMetricsCardProps) {
  const engagementColor =
    metrics.engagementScore >= 70
      ? 'text-green-400'
      : metrics.engagementScore >= 50
        ? 'text-amber-400'
        : 'text-red-400';

  const bounceColor =
    metrics.bounceRate < 35
      ? 'text-green-400'
      : metrics.bounceRate < 50
        ? 'text-amber-400'
        : 'text-red-400';

  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <h2 className="text-sm font-semibold text-gray-200 mb-4">Performance</h2>
      <div className="grid grid-cols-2 gap-4">
        <div className="bg-gray-900/50 rounded-lg p-3">
          <p className="text-xs text-gray-400 mb-1">Views</p>
          <p className="text-2xl font-bold text-white">
            {metrics.views.toLocaleString()}
          </p>
        </div>
        <div className="bg-gray-900/50 rounded-lg p-3">
          <p className="text-xs text-gray-400 mb-1">Engagement</p>
          <p className={`text-2xl font-bold ${engagementColor}`}>
            {metrics.engagementScore}
          </p>
        </div>
        <div className="bg-gray-900/50 rounded-lg p-3">
          <p className="text-xs text-gray-400 mb-1">Avg Time</p>
          <p className="text-2xl font-bold text-white">
            {formatAvgTime(metrics.avgTimeOnPageSec)}
          </p>
        </div>
        <div className="bg-gray-900/50 rounded-lg p-3">
          <p className="text-xs text-gray-400 mb-1">Bounce Rate</p>
          <p className={`text-2xl font-bold ${bounceColor}`}>
            {metrics.bounceRate}%
          </p>
        </div>
      </div>
    </div>
  );
}
