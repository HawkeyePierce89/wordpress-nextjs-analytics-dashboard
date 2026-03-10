'use client';

import type { ActivityEvent } from '@/types';

type RecentActivityProps = {
  events: ActivityEvent[];
};

const dotColors: Record<ActivityEvent['type'], string> = {
  published: 'bg-green-400',
  seo_updated: 'bg-blue-400',
  created: 'bg-amber-400',
  featured_changed: 'bg-red-400',
  updated: 'bg-gray-400',
};

function getRelativeTime(dateStr: string): string {
  const date = new Date(dateStr);
  const now = new Date();
  const diffMs = now.getTime() - date.getTime();
  const diffSec = Math.floor(diffMs / 1000);
  const diffMin = Math.floor(diffSec / 60);
  const diffHr = Math.floor(diffMin / 60);
  const diffDay = Math.floor(diffHr / 24);

  if (diffDay > 0) return `${diffDay}d ago`;
  if (diffHr > 0) return `${diffHr}h ago`;
  if (diffMin > 0) return `${diffMin}m ago`;
  return 'just now';
}

export function RecentActivity({ events }: RecentActivityProps) {
  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <h2 className="text-sm font-semibold text-gray-200 mb-4">
        Recent Activity
      </h2>
      <ul className="space-y-3">
        {events.map((event) => (
          <li key={event.id} className="flex items-start gap-3">
            <span
              className={`mt-1.5 h-2 w-2 shrink-0 rounded-full ${dotColors[event.type]}`}
            />
            <div className="min-w-0 flex-1">
              <p className="text-sm text-gray-200 leading-snug">{event.message}</p>
              <p className="text-xs text-gray-500 mt-0.5">
                {event.user} &middot; {getRelativeTime(event.createdAt)}
              </p>
            </div>
          </li>
        ))}
      </ul>
    </div>
  );
}
