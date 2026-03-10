'use client';

import {
  useQuery,
  useMutation,
  useQueryClient,
  type UseQueryResult,
} from '@tanstack/react-query';
import {
  fetchOverview,
  fetchPosts,
  fetchPost,
  fetchAnalytics,
  fetchAuthors,
  fetchCategories,
  updatePostMetadata,
} from './api';
import type {
  OverviewResponse,
  PaginatedPostsResponse,
  PostDetailsResponse,
  AnalyticsResponse,
  Author,
  Category,
  UpdateMetadataRequest,
  Post,
  PostsFilterState,
} from '@/types';

export function useOverview(): UseQueryResult<OverviewResponse> {
  return useQuery({
    queryKey: ['overview'],
    queryFn: fetchOverview,
  });
}

export function usePosts(
  filters: Partial<PostsFilterState> = {}
): UseQueryResult<PaginatedPostsResponse> {
  return useQuery({
    queryKey: ['posts', filters],
    queryFn: () => fetchPosts(filters),
  });
}

export function usePost(id: number): UseQueryResult<PostDetailsResponse> {
  return useQuery({
    queryKey: ['post', id],
    queryFn: () => fetchPost(id),
    enabled: !!id,
  });
}

export function useAnalytics(): UseQueryResult<AnalyticsResponse> {
  return useQuery({
    queryKey: ['analytics'],
    queryFn: fetchAnalytics,
  });
}

export function useAuthors(): UseQueryResult<Author[]> {
  return useQuery({
    queryKey: ['authors'],
    queryFn: fetchAuthors,
  });
}

export function useCategories(): UseQueryResult<Category[]> {
  return useQuery({
    queryKey: ['categories'],
    queryFn: fetchCategories,
  });
}

export function useUpdateMetadata(postId: number) {
  const queryClient = useQueryClient();
  return useMutation<Post, Error, UpdateMetadataRequest>({
    mutationFn: (data) => updatePostMetadata(postId, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['post', postId] });
      queryClient.invalidateQueries({ queryKey: ['posts'] });
    },
  });
}
