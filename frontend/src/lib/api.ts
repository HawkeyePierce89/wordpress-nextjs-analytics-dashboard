import type {
  PaginatedPostsResponse,
  PostDetailsResponse,
  OverviewResponse,
  AnalyticsResponse,
  Author,
  Category,
  UpdateMetadataRequest,
  Post,
  PostsFilterState,
} from '@/types';

export const API_BASE =
  process.env.NEXT_PUBLIC_WP_API_URL ||
  'http://localhost:8080/wp-json/dashboard/v1';

// ─── Case transforms ──────────────────────────────────────────────────────────

export function toCamelCase(str: string): string {
  return str.replace(/_([a-z])/g, (_, letter: string) => letter.toUpperCase());
}

export function toSnakeCase(str: string): string {
  return str.replace(/([A-Z])/g, (letter: string) => `_${letter.toLowerCase()}`);
}

type JsonValue =
  | string
  | number
  | boolean
  | null
  | JsonValue[]
  | { [key: string]: JsonValue };

export function transformKeys(obj: JsonValue): JsonValue {
  if (Array.isArray(obj)) {
    return obj.map(transformKeys);
  }
  if (obj !== null && typeof obj === 'object') {
    return Object.fromEntries(
      Object.entries(obj).map(([k, v]) => [toCamelCase(k), transformKeys(v)])
    );
  }
  return obj;
}

export function transformKeysToSnake(obj: JsonValue): JsonValue {
  if (Array.isArray(obj)) {
    return obj.map(transformKeysToSnake);
  }
  if (obj !== null && typeof obj === 'object') {
    return Object.fromEntries(
      Object.entries(obj).map(([k, v]) => [toSnakeCase(k), transformKeysToSnake(v)])
    );
  }
  return obj;
}

// ─── Core fetch wrapper ───────────────────────────────────────────────────────

export async function apiFetch<T>(
  path: string,
  options: RequestInit = {}
): Promise<T> {
  const url = `${API_BASE}${path}`;
  const res = await fetch(url, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      ...(options.headers ?? {}),
    },
  });

  if (!res.ok) {
    let message = `API error ${res.status}`;
    try {
      const body = await res.json();
      if (body?.message) message = body.message;
    } catch {
      // ignore parse error
    }
    throw new Error(message);
  }

  const json = await res.json();
  return transformKeys(json as JsonValue) as T;
}

// ─── Exported API functions ───────────────────────────────────────────────────

export async function fetchPosts(
  filters: Partial<PostsFilterState> = {}
): Promise<PaginatedPostsResponse> {
  const params = new URLSearchParams();
  if (filters.search) params.set('search', filters.search);
  if (filters.status) params.set('status', filters.status);
  if (filters.authorId) params.set('author_id', String(filters.authorId));
  if (filters.categoryId) params.set('category_id', String(filters.categoryId));
  if (filters.page) params.set('page', String(filters.page));
  if (filters.perPage) params.set('per_page', String(filters.perPage));
  if (filters.sortBy) params.set('sort_by', filters.sortBy);
  if (filters.sortOrder) params.set('sort_order', filters.sortOrder);
  const qs = params.toString() ? `?${params.toString()}` : '';
  return apiFetch<PaginatedPostsResponse>(`/posts${qs}`);
}

export async function fetchPost(id: number): Promise<PostDetailsResponse> {
  return apiFetch<PostDetailsResponse>(`/posts/${id}`);
}

export async function fetchOverview(): Promise<OverviewResponse> {
  return apiFetch<OverviewResponse>('/overview');
}

export async function fetchAnalytics(): Promise<AnalyticsResponse> {
  return apiFetch<AnalyticsResponse>('/analytics');
}

export async function fetchAuthors(): Promise<Author[]> {
  return apiFetch<Author[]>('/authors');
}

export async function fetchCategories(): Promise<Category[]> {
  return apiFetch<Category[]>('/categories');
}

export async function updatePostMetadata(
  id: number,
  data: UpdateMetadataRequest
): Promise<Post> {
  return apiFetch<Post>(`/posts/${id}/metadata`, {
    method: 'PATCH',
    body: JSON.stringify(transformKeysToSnake(data as unknown as JsonValue)),
  });
}
