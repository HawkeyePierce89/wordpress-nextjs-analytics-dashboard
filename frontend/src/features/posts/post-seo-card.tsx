'use client';

import type { PostSeo, Post } from '@/types';

type PostSeoCardProps = {
  seo: PostSeo;
  post: Post;
};

export function PostSeoCard({ seo, post }: PostSeoCardProps) {
  const displayTitle = seo.title || post.title;
  const displayDescription = seo.description || post.excerpt;

  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <h2 className="text-sm font-semibold text-gray-200 mb-4">SEO</h2>
      <div className="space-y-3 mb-4">
        <div>
          <p className="text-xs text-gray-400 mb-1">SEO Title</p>
          <p className="text-sm text-gray-200">
            {seo.title || <span className="text-gray-500 italic">Not set</span>}
          </p>
        </div>
        <div>
          <p className="text-xs text-gray-400 mb-1">SEO Description</p>
          <p className="text-sm text-gray-200">
            {seo.description || <span className="text-gray-500 italic">Not set</span>}
          </p>
        </div>
      </div>
      <div className="bg-gray-900 rounded-lg p-3">
        <p className="text-xs text-gray-500 mb-2">Google Preview</p>
        <p className="text-sm text-blue-400 hover:underline cursor-pointer truncate">
          {displayTitle}
        </p>
        <p className="text-xs text-green-600 truncate">
          yourdomain.com/posts/{post.slug}
        </p>
        <p className="text-xs text-gray-400 mt-1 line-clamp-2">{displayDescription}</p>
      </div>
    </div>
  );
}
