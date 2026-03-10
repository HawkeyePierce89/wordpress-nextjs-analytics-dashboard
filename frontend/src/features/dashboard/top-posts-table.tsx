'use client';

type TopPost = {
  id: number;
  title: string;
  views: number;
};

type TopPostsTableProps = {
  topPosts: TopPost[];
};

function formatViews(n: number): string {
  if (n >= 1_000_000) return `${(n / 1_000_000).toFixed(1)}M`;
  if (n >= 1_000) return `${(n / 1_000).toFixed(1)}K`;
  return String(n);
}

export function TopPostsTable({ topPosts }: TopPostsTableProps) {
  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <h2 className="text-sm font-semibold text-gray-200 mb-4">
        Top Performing Posts
      </h2>
      <ul className="space-y-2">
        {topPosts.map((post, index) => (
          <li
            key={post.id}
            className="flex items-center justify-between py-2 border-b border-gray-700/50 last:border-0"
          >
            <div className="flex items-center gap-3 min-w-0">
              <span className="text-xs text-gray-500 w-5 shrink-0 text-right">
                {index + 1}
              </span>
              <span className="text-sm text-gray-200 truncate">{post.title}</span>
            </div>
            <span className="text-sm font-medium text-green-400 shrink-0 ml-4">
              {formatViews(post.views)} views
            </span>
          </li>
        ))}
      </ul>
    </div>
  );
}
