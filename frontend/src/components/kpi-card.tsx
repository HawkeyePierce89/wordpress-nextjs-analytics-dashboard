type KpiCardProps = {
  title: string;
  value: string | number;
  delta?: {
    value: string;
    positive: boolean;
  };
};

export function KpiCard({ title, value, delta }: KpiCardProps) {
  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <p className="uppercase text-xs text-gray-500 tracking-wide font-medium mb-2">
        {title}
      </p>
      <p className="text-2xl font-bold text-white">{value}</p>
      {delta && (
        <p
          className={`text-xs mt-1 flex items-center gap-0.5 ${
            delta.positive ? 'text-green-400' : 'text-red-400'
          }`}
        >
          <span>{delta.positive ? '↑' : '↓'}</span>
          <span>{delta.value}</span>
        </p>
      )}
    </div>
  );
}
