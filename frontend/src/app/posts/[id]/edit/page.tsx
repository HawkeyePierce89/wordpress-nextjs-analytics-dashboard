'use client';

import { use } from 'react';
import Link from 'next/link';
import { usePost } from '@/lib/queries';
import { ErrorState } from '@/components/error-state';
import { Skeleton } from '@/components/skeleton';
import { EditMetadataForm } from '@/features/edit-metadata/edit-metadata-form';

type Params = Promise<{ id: string }>;

export default function EditPostPage({ params }: { params: Params }) {
  const { id } = use(params);
  const { data, isLoading, isError, refetch } = usePost(Number(id));

  if (isLoading) {
    return (
      <div className="space-y-4 max-w-2xl">
        <Skeleton className="h-4 w-40" />
        <Skeleton className="h-8 w-48" />
        <Skeleton className="h-64 w-full" />
      </div>
    );
  }

  if (isError || !data) {
    return (
      <ErrorState
        message="Failed to load post."
        onRetry={() => refetch()}
      />
    );
  }

  const { post } = data;

  return (
    <div className="max-w-2xl space-y-6">
      {/* Breadcrumb */}
      <nav>
        <Link
          href={`/posts/${post.id}`}
          className="text-sm text-blue-400 hover:text-blue-300 transition-colors"
        >
          ← {post.title}
        </Link>
      </nav>

      <h1 className="text-2xl font-bold text-white">Edit Metadata</h1>

      <EditMetadataForm post={post} />
    </div>
  );
}
