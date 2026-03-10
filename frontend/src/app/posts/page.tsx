'use client';

import { Suspense } from 'react';
import { PostsListContent } from '@/features/posts/posts-list-content';

export default function PostsPage() {
  return (
    <Suspense>
      <PostsListContent />
    </Suspense>
  );
}
