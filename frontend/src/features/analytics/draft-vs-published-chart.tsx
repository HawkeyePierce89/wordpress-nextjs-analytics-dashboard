'use client';

import {
  ResponsiveContainer,
  PieChart,
  Pie,
  Cell,
  Tooltip,
  Legend,
} from 'recharts';

type DraftVsPublishedChartProps = {
  draftVsPublished: { draft: number; published: number; scheduled: number };
};

const COLORS = {
  published: '#4ade80',
  draft: '#fbbf24',
  scheduled: '#60a5fa',
};

export function DraftVsPublishedChart({ draftVsPublished }: DraftVsPublishedChartProps) {
  const data = [
    { name: 'Published', value: draftVsPublished.published, color: COLORS.published },
    { name: 'Draft', value: draftVsPublished.draft, color: COLORS.draft },
    { name: 'Scheduled', value: draftVsPublished.scheduled, color: COLORS.scheduled },
  ].filter((d) => d.value > 0);

  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <h2 className="text-sm font-semibold text-gray-200 mb-4">Draft vs Published</h2>
      <ResponsiveContainer width="100%" height={220}>
        <PieChart>
          <Pie
            data={data}
            cx="40%"
            cy="50%"
            innerRadius={60}
            outerRadius={80}
            dataKey="value"
            paddingAngle={2}
          >
            {data.map((entry, index) => (
              <Cell key={index} fill={entry.color} />
            ))}
          </Pie>
          <Tooltip
            contentStyle={{
              backgroundColor: '#1f2937',
              border: '1px solid #374151',
              borderRadius: '6px',
              color: '#f3f4f6',
              fontSize: 12,
            }}
          />
          <Legend
            layout="vertical"
            align="right"
            verticalAlign="middle"
            iconType="circle"
            iconSize={8}
            formatter={(value) => (
              <span style={{ color: '#d1d5db', fontSize: 12 }}>{value}</span>
            )}
          />
        </PieChart>
      </ResponsiveContainer>
    </div>
  );
}
