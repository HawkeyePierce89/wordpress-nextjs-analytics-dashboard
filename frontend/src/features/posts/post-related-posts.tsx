'use client';

import Link from 'next/link';

type RelatedPost = {
  id: number;
  title: string;
  slug: string;
};

type PostRelatedPostsProps = {
  relatedPosts: RelatedPost[];
};

export function PostRelatedPosts({ relatedPosts }: PostRelatedPostsProps) {
  if (relatedPosts.length === 0) return null;

  return (
    <div className="bg-gray-800 rounded-lg p-4">
      <h2 className="text-sm font-semibold text-gray-200 mb-3">Related Posts</h2>
      <ul className="space-y-2">
        {relatedPosts.map((related) => (
          <li key={related.id}>
            <Link
              href={`/posts/${related.id}`}
              className="text-sm text-blue-400 hover:text-blue-300 transition-colors line-clamp-2"
            >
              {related.title}
            </Link>
          </li>
        ))}
      </ul>
    </div>
  );
}
