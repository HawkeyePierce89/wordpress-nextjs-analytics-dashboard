export type PostStatus = 'draft' | 'published' | 'scheduled';

export type Category = {
  id: number;
  name: string;
  slug: string;
};

export type Author = {
  id: number;
  name: string;
  avatarUrl: string;
  role: string;
};

export type PostMetrics = {
  views: number;
  engagementScore: number;
  avgTimeOnPageSec: number;
  bounceRate: number;
};

export type PostSeo = {
  title: string;
  description: string;
};

export type Post = {
  id: number;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  status: PostStatus;
  author: Author;
  categories: Category[];
  featuredImageUrl: string | null;
  publishedAt: string | null;
  updatedAt: string;
  readingTimeMinutes: number;
  seo: PostSeo;
  metrics: PostMetrics;
  isFeatured: boolean;
  editorNote: string | null;
};

export type ActivityEvent = {
  id: number;
  type: 'created' | 'updated' | 'published' | 'seo_updated' | 'featured_changed';
  user: string;
  createdAt: string;
  message: string;
};

export type PaginatedPostsResponse = {
  items: Post[];
  total: number;
  page: number;
  perPage: number;
  totalPages: number;
};

export type PostDetailsResponse = {
  post: Post;
  activity: ActivityEvent[];
  relatedPosts: { id: number; title: string; slug: string }[];
};

export type OverviewResponse = {
  totalPosts: number;
  publishedPosts: number;
  draftPosts: number;
  scheduledPosts: number;
  totalAuthors: number;
  avgReadingTime: number;
  totalViews: number;
  avgEngagementScore: number;
  postsPerMonth: { month: string; count: number }[];
  postsByCategory: { category: string; count: number }[];
  topPosts: { id: number; title: string; views: number }[];
  recentActivity: ActivityEvent[];
};

export type AnalyticsResponse = {
  postsPerMonth: { month: string; count: number }[];
  topCategories: { category: string; count: number }[];
  topAuthors: { author: string; count: number }[];
  draftVsPublished: { draft: number; published: number; scheduled: number };
  avgReadingTimeByCategory: { category: string; avgMinutes: number }[];
  topPostsByViews: { id: number; title: string; views: number }[];
  contentHealth: {
    missingSeoDescriptionPct: number;
    avgReadingTime: number;
    topCategory: string;
    topAuthor: string;
  };
};

export type UpdateMetadataRequest = {
  seoTitle?: string;
  seoDescription?: string;
  readingTimeMinutes?: number;
  views?: number;
  engagementScore?: number;
  isFeatured?: boolean;
  editorNote?: string;
};

export type PostsFilterState = {
  search: string;
  status: PostStatus | '';
  authorId: number | '';
  categoryId: number | '';
  page: number;
  perPage: number;
  sortBy: 'date' | 'title' | 'views' | 'engagement';
  sortOrder: 'asc' | 'desc';
};
