import type { PostStatus } from '@/types';

const statusStyles: Record<PostStatus, string> = {
  published: 'bg-green-900/50 text-green-400',
  draft: 'bg-amber-900/50 text-amber-400',
  scheduled: 'bg-blue-900/50 text-blue-400',
};

type StatusBadgeProps = {
  status: PostStatus;
};

export function StatusBadge({ status }: StatusBadgeProps) {
  return (
    <span
      className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs capitalize ${statusStyles[status]}`}
    >
      {status}
    </span>
  );
}
