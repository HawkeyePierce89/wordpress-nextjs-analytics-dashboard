type EmptyStateProps = {
  message: string;
};

export function EmptyState({ message }: EmptyStateProps) {
  return (
    <div className="flex items-center justify-center py-12 text-center">
      <p className="text-gray-500 text-sm">{message}</p>
    </div>
  );
}
