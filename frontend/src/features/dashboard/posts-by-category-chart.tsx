'use client';

import {
  ResponsiveContainer,
  BarChart,
  Bar,
  XAxis,
  YAxis,
  Tooltip,
  Cell,
} from 'recharts';

type PostsByCategoryChartProps = {
  postsByCategory: { category: string; count: number }[];
};

export function PostsByCategoryChart({ postsByCategory }: PostsByCategoryChartProps) {
  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <h2 className="text-sm font-semibold text-gray-200 mb-4">
        Posts by Category
      </h2>
      <ResponsiveContainer width="100%" height={220}>
        <BarChart
          data={postsByCategory}
          layout="vertical"
          margin={{ top: 5, right: 20, left: 10, bottom: 0 }}
        >
          <XAxis
            type="number"
            tick={{ fill: '#6b7280', fontSize: 11 }}
            axisLine={false}
            tickLine={false}
            allowDecimals={false}
          />
          <YAxis
            type="category"
            dataKey="category"
            tick={{ fill: '#6b7280', fontSize: 11 }}
            axisLine={false}
            tickLine={false}
            width={90}
          />
          <Tooltip
            contentStyle={{
              backgroundColor: '#1f2937',
              border: '1px solid #374151',
              borderRadius: '6px',
              color: '#f3f4f6',
              fontSize: 12,
            }}
            itemStyle={{ color: '#60a5fa' }}
            cursor={{ fill: 'rgba(96, 165, 250, 0.05)' }}
          />
          <Bar dataKey="count" radius={[0, 4, 4, 0]}>
            {postsByCategory.map((_entry, index) => (
              <Cell key={`cell-${index}`} fill="#60a5fa" />
            ))}
          </Bar>
        </BarChart>
      </ResponsiveContainer>
    </div>
  );
}
