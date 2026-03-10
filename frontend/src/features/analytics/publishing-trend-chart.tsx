'use client';

import {
  ResponsiveContainer,
  AreaChart,
  Area,
  XAxis,
  YAxis,
  Tooltip,
  CartesianGrid,
} from 'recharts';

type PublishingTrendChartProps = {
  postsPerMonth: { month: string; count: number }[];
};

function formatMonthLabel(month: string): string {
  try {
    const [year, mon] = month.split('-');
    const date = new Date(Number(year), Number(mon) - 1, 1);
    return date.toLocaleString('en-US', { month: 'short' });
  } catch {
    return month;
  }
}

export function PublishingTrendChart({ postsPerMonth }: PublishingTrendChartProps) {
  const data = postsPerMonth.map((d) => ({
    ...d,
    label: formatMonthLabel(d.month),
  }));

  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <h2 className="text-sm font-semibold text-gray-200 mb-4">Publishing Trend</h2>
      <ResponsiveContainer width="100%" height={220}>
        <AreaChart data={data} margin={{ top: 5, right: 10, left: -20, bottom: 0 }}>
          <defs>
            <linearGradient id="publishTrendGradient" x1="0" y1="0" x2="0" y2="1">
              <stop offset="5%" stopColor="#60a5fa" stopOpacity={0.3} />
              <stop offset="95%" stopColor="#60a5fa" stopOpacity={0} />
            </linearGradient>
          </defs>
          <CartesianGrid strokeDasharray="3 3" stroke="#374151" />
          <XAxis
            dataKey="label"
            tick={{ fill: '#6b7280', fontSize: 11 }}
            axisLine={false}
            tickLine={false}
          />
          <YAxis
            tick={{ fill: '#6b7280', fontSize: 11 }}
            axisLine={false}
            tickLine={false}
            allowDecimals={false}
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
            cursor={{ stroke: '#374151' }}
          />
          <Area
            type="monotone"
            dataKey="count"
            stroke="#60a5fa"
            strokeWidth={2}
            fill="url(#publishTrendGradient)"
            dot={false}
            activeDot={{ r: 4, fill: '#60a5fa' }}
          />
        </AreaChart>
      </ResponsiveContainer>
    </div>
  );
}
