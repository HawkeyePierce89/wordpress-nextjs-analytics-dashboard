'use client';

import type { ActivityEvent } from '@/types';

type PostActivityTimelineProps = {
  activity: ActivityEvent[];
};

const dotColors: Record<ActivityEvent['type'], string> = {
  published: 'bg-green-500',
  seo_updated: 'bg-blue-500',
  created: 'bg-amber-500',
  featured_changed: 'bg-red-500',
  updated: 'bg-gray-500',
};

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

export function PostActivityTimeline({ activity }: PostActivityTimelineProps) {
  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <h2 className="text-sm font-semibold text-gray-200 mb-4">Activity</h2>
      {activity.length === 0 ? (
        <p className="text-xs text-gray-500">No activity recorded.</p>
      ) : (
        <div className="relative">
          <div className="absolute left-[7px] top-0 bottom-0 w-px bg-gray-700" />
          <ul className="space-y-4">
            {activity.map((event) => (
              <li key={event.id} className="flex items-start gap-3 pl-5 relative">
                <span
                  className={`absolute left-0 top-1 w-3.5 h-3.5 rounded-full flex-shrink-0 ${dotColors[event.type]}`}
                />
                <div className="min-w-0">
                  <p className="text-xs text-gray-200">{event.message}</p>
                  <p className="text-xs text-gray-500 mt-0.5">{formatDate(event.createdAt)}</p>
                </div>
              </li>
            ))}
          </ul>
        </div>
      )}
    </div>
  );
}
