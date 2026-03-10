'use client';

import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { useRouter } from 'next/navigation';
import type { Post } from '@/types';
import { useUpdateMetadata } from '@/lib/queries';
import { metadataSchema, type MetadataFormValues } from './schema';

type EditMetadataFormProps = {
  post: Post;
};

const inputClass =
  'w-full bg-gray-800 border border-gray-700 text-white text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-500';

const labelClass = 'block text-xs text-gray-400 mb-1';

export function EditMetadataForm({ post }: EditMetadataFormProps) {
  const router = useRouter();
  const [successMessage, setSuccessMessage] = useState<string | null>(null);
  const [mutationError, setMutationError] = useState<string | null>(null);
  const mutation = useUpdateMetadata(post.id);

  const {
    register,
    handleSubmit,
    watch,
    formState: { errors, isDirty, isValid, isSubmitting },
  } = useForm<MetadataFormValues>({
    resolver: zodResolver(metadataSchema),
    defaultValues: {
      seoTitle: post.seo.title ?? '',
      seoDescription: post.seo.description ?? '',
      readingTimeMinutes: post.readingTimeMinutes,
      views: post.metrics.views,
      engagementScore: post.metrics.engagementScore,
      isFeatured: post.isFeatured,
      editorNote: post.editorNote ?? '',
    },
  });

  const seoTitle = watch('seoTitle') ?? '';
  const seoDescription = watch('seoDescription') ?? '';

  async function onSubmit(values: MetadataFormValues) {
    setMutationError(null);
    try {
      await mutation.mutateAsync({
        seoTitle: values.seoTitle || undefined,
        seoDescription: values.seoDescription || undefined,
        readingTimeMinutes: values.readingTimeMinutes,
        views: values.views,
        engagementScore: values.engagementScore,
        isFeatured: values.isFeatured,
        editorNote: values.editorNote || undefined,
      });
      setSuccessMessage('Metadata saved successfully!');
      setTimeout(() => {
        router.push(`/posts/${post.id}`);
      }, 1200);
    } catch (err) {
      setMutationError(err instanceof Error ? err.message : 'Failed to save metadata.');
    }
  }

  return (
    <div className="space-y-6">
      {successMessage && (
        <div className="bg-green-900/50 border border-green-700 text-green-400 text-sm rounded-md px-4 py-3">
          {successMessage}
        </div>
      )}

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
        {/* SEO Title */}
        <div>
          <div className="flex items-center justify-between mb-1">
            <label className={labelClass}>SEO Title</label>
            <span className="text-xs text-gray-500">{seoTitle.length}/60</span>
          </div>
          <input
            {...register('seoTitle')}
            type="text"
            className={inputClass}
            placeholder="SEO title..."
          />
          {errors.seoTitle && (
            <p className="text-xs text-red-400 mt-1">{errors.seoTitle.message}</p>
          )}
        </div>

        {/* SEO Description */}
        <div>
          <div className="flex items-center justify-between mb-1">
            <label className={labelClass}>SEO Description</label>
            <span className="text-xs text-gray-500">{seoDescription.length}/160</span>
          </div>
          <textarea
            {...register('seoDescription')}
            rows={3}
            className={inputClass}
            placeholder="SEO description..."
          />
          {errors.seoDescription && (
            <p className="text-xs text-red-400 mt-1">{errors.seoDescription.message}</p>
          )}
        </div>

        {/* Reading time, views, engagement */}
        <div className="grid grid-cols-3 gap-4">
          <div>
            <label className={labelClass}>Reading Time (min)</label>
            <input
              {...register('readingTimeMinutes', { valueAsNumber: true })}
              type="number"
              min={1}
              className={inputClass}
            />
            {errors.readingTimeMinutes && (
              <p className="text-xs text-red-400 mt-1">{errors.readingTimeMinutes.message}</p>
            )}
          </div>
          <div>
            <label className={labelClass}>Views</label>
            <input
              {...register('views', { valueAsNumber: true })}
              type="number"
              min={0}
              className={inputClass}
            />
            {errors.views && (
              <p className="text-xs text-red-400 mt-1">{errors.views.message}</p>
            )}
          </div>
          <div>
            <label className={labelClass}>Engagement Score</label>
            <input
              {...register('engagementScore', { valueAsNumber: true })}
              type="number"
              min={0}
              max={100}
              className={inputClass}
            />
            {errors.engagementScore && (
              <p className="text-xs text-red-400 mt-1">{errors.engagementScore.message}</p>
            )}
          </div>
        </div>

        {/* Featured Toggle */}
        <div className="flex items-center justify-between">
          <label className="text-sm text-gray-400">Featured Post</label>
          <label className="relative inline-flex items-center cursor-pointer">
            <input
              {...register('isFeatured')}
              type="checkbox"
              className="sr-only peer"
            />
            <div className="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600" />
          </label>
        </div>

        {/* Editor Note */}
        <div>
          <label className={labelClass}>Editor Note</label>
          <textarea
            {...register('editorNote')}
            rows={3}
            className={inputClass}
            placeholder="Internal editor note..."
          />
        </div>

        {/* Error */}
        {mutationError && (
          <p className="text-sm text-red-400">{mutationError}</p>
        )}

        {/* Actions */}
        <div className="flex items-center gap-3 pt-2">
          <button
            type="submit"
            disabled={!isDirty || !isValid || isSubmitting}
            className="px-4 py-2 bg-blue-600 hover:bg-blue-500 disabled:bg-gray-700 disabled:text-gray-500 disabled:cursor-not-allowed text-white text-sm rounded-md transition-colors font-medium"
          >
            {isSubmitting ? 'Saving…' : 'Save Changes'}
          </button>
          <button
            type="button"
            onClick={() => router.push(`/posts/${post.id}`)}
            className="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm rounded-md transition-colors"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  );
}
