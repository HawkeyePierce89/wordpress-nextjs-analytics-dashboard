'use client';

import { use } from 'react';
import Link from 'next/link';
import { usePost } from '@/lib/queries';
import { StatusBadge } from '@/components/status-badge';
import { ErrorState } from '@/components/error-state';
import { Skeleton } from '@/components/skeleton';
import { PostMetadataCard } from '@/features/posts/post-metadata-card';
import { PostMetricsCard } from '@/features/posts/post-metrics-card';
import { PostSeoCard } from '@/features/posts/post-seo-card';
import { PostActivityTimeline } from '@/features/posts/post-activity-timeline';
import { PostRelatedPosts } from '@/features/posts/post-related-posts';

function stripHtml(html: string): string {
  return html.replace(/<[^>]*>/g, '');
}

function PostDetailsSkeleton() {
  return (
    <div className="space-y-6">
      <div className="space-y-2">
        <Skeleton className="h-4 w-40" />
        <Skeleton className="h-8 w-2/3" />
        <Skeleton className="h-4 w-1/4" />
      </div>
      <div className="grid gap-6" style={{ gridTemplateColumns: '1fr 320px' }}>
        <div className="space-y-4">
          <Skeleton className="h-24 w-full" />
          <Skeleton className="h-48 w-full" />
        </div>
        <div className="space-y-4">
          <Skeleton className="h-40 w-full" />
          <Skeleton className="h-40 w-full" />
          <Skeleton className="h-40 w-full" />
        </div>
      </div>
    </div>
  );
}

type Params = Promise<{ id: string }>;

export default function PostDetailsPage({ params }: { params: Params }) {
  const { id } = use(params);
  const { data, isLoading, isError, refetch } = usePost(Number(id));

  if (isLoading) return <PostDetailsSkeleton />;

  if (isError || !data) {
    return (
      <ErrorState
        message="Failed to load post details."
        onRetry={() => refetch()}
      />
    );
  }

  const { post, activity, relatedPosts } = data;
  const plainContent = stripHtml(post.content);
  const previewContent =
    plainContent.length > 500 ? plainContent.slice(0, 500) + '…' : plainContent;

  return (
    <div className="space-y-6">
      {/* Breadcrumb */}
      <nav className="flex items-center gap-2 text-sm">
        <Link href="/posts" className="text-blue-400 hover:text-blue-300 transition-colors">
          ← Posts
        </Link>
        <span className="text-gray-500">/</span>
        <span className="text-gray-300 truncate max-w-xs">{post.title}</span>
      </nav>

      {/* Header */}
      <div className="flex items-center gap-3 flex-wrap">
        <h1 className="text-2xl font-bold text-white leading-tight">{post.title}</h1>
        <StatusBadge status={post.status} />
      </div>
      <p className="text-sm text-gray-400 -mt-4">/{post.slug}</p>

      {/* Two-column layout */}
      <div className="grid gap-6" style={{ gridTemplateColumns: '1fr 320px' }}>
        {/* Left column */}
        <div className="space-y-4 min-w-0">
          {/* Excerpt */}
          {post.excerpt && (
            <div className="bg-gray-800 rounded-lg p-4">
              <h2 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                Excerpt
              </h2>
              <p className="text-sm text-gray-300 leading-relaxed">{post.excerpt}</p>
            </div>
          )}

          {/* Content Preview */}
          <div className="bg-gray-800 rounded-lg p-4">
            <h2 className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
              Content Preview
            </h2>
            <p className="text-sm text-gray-300 leading-relaxed whitespace-pre-line">
              {previewContent || <span className="text-gray-500 italic">No content</span>}
            </p>
          </div>

          {/* Related Posts */}
          <PostRelatedPosts relatedPosts={relatedPosts} />
        </div>

        {/* Right sidebar */}
        <div className="space-y-4">
          <PostMetadataCard post={post} />
          <PostMetricsCard metrics={post.metrics} />
          <PostSeoCard seo={post.seo} post={post} />
          <PostActivityTimeline activity={activity} />
        </div>
      </div>
    </div>
  );
}
