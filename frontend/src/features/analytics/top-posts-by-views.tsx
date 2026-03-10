'use client';

type TopPostsByViewsProps = {
  topPostsByViews: { id: number; title: string; views: number }[];
};

export function TopPostsByViews({ topPostsByViews }: TopPostsByViewsProps) {
  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <h2 className="text-sm font-semibold text-gray-200 mb-4">Top 5 Posts by Views</h2>
      {topPostsByViews.length === 0 ? (
        <p className="text-sm text-gray-500">No data available.</p>
      ) : (
        <ul className="space-y-3">
          {topPostsByViews.slice(0, 5).map((post) => (
            <li key={post.id} className="flex items-center justify-between gap-4">
              <span className="text-sm text-gray-300 truncate">{post.title}</span>
              <span className="text-sm font-medium text-green-400 flex-shrink-0">
                {post.views.toLocaleString()}
              </span>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}
