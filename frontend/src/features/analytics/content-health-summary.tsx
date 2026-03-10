'use client';

type ContentHealthSummaryProps = {
  contentHealth: {
    missingSeoDescriptionPct: number;
    avgReadingTime: number;
    topCategory: string;
    topAuthor: string;
  };
};

export function ContentHealthSummary({ contentHealth }: ContentHealthSummaryProps) {
  const { missingSeoDescriptionPct, avgReadingTime, topCategory, topAuthor } = contentHealth;

  const items = [
    {
      dotColor: 'bg-red-500',
      text: `${missingSeoDescriptionPct}% of posts have no SEO description`,
    },
    {
      dotColor: 'bg-green-500',
      text: `Average reading time: ${avgReadingTime} min`,
    },
    {
      dotColor: 'bg-blue-500',
      text: `Top category: ${topCategory}`,
    },
    {
      dotColor: 'bg-amber-500',
      text: `Top author: ${topAuthor}`,
    },
  ];

  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <h2 className="text-sm font-semibold text-gray-200 mb-4">Content Health Summary</h2>
      <ul className="space-y-3">
        {items.map((item, i) => (
          <li key={i} className="flex items-center gap-3">
            <span className={`w-2.5 h-2.5 rounded-full flex-shrink-0 ${item.dotColor}`} />
            <span className="text-sm text-gray-300">{item.text}</span>
          </li>
        ))}
      </ul>
    </div>
  );
}
