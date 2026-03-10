'use client';

import {
  ResponsiveContainer,
  BarChart,
  Bar,
  XAxis,
  YAxis,
  Tooltip,
  CartesianGrid,
  Cell,
} from 'recharts';

type ReadingTimeByCategoryChartProps = {
  avgReadingTimeByCategory: { category: string; avgMinutes: number }[];
};

const BAR_COLORS = [
  '#60a5fa',
  '#4ade80',
  '#fbbf24',
  '#f87171',
  '#a78bfa',
  '#34d399',
  '#fb923c',
];

export function ReadingTimeByCategoryChart({
  avgReadingTimeByCategory,
}: ReadingTimeByCategoryChartProps) {
  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <h2 className="text-sm font-semibold text-gray-200 mb-4">
        Average Reading Time by Category
      </h2>
      <ResponsiveContainer width="100%" height={220}>
        <BarChart
          data={avgReadingTimeByCategory}
          layout="vertical"
          margin={{ top: 0, right: 10, left: 10, bottom: 0 }}
        >
          <CartesianGrid strokeDasharray="3 3" stroke="#374151" horizontal={false} />
          <XAxis
            type="number"
            tick={{ fill: '#6b7280', fontSize: 11 }}
            axisLine={false}
            tickLine={false}
            allowDecimals={false}
            unit=" min"
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
            formatter={(value) => [`${value} min`, 'Avg Reading Time']}
            contentStyle={{
              backgroundColor: '#1f2937',
              border: '1px solid #374151',
              borderRadius: '6px',
              color: '#f3f4f6',
              fontSize: 12,
            }}
            cursor={{ fill: '#374151' }}
          />
          <Bar dataKey="avgMinutes" radius={[0, 4, 4, 0]}>
            {avgReadingTimeByCategory.map((_, index) => (
              <Cell key={index} fill={BAR_COLORS[index % BAR_COLORS.length]} />
            ))}
          </Bar>
        </BarChart>
      </ResponsiveContainer>
    </div>
  );
}
