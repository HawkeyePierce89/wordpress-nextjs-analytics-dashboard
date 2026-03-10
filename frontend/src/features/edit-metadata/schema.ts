import { z } from 'zod';

export const metadataSchema = z.object({
  seoTitle: z.string().max(60, 'Max 60 characters').optional().or(z.literal('')),
  seoDescription: z.string().max(160, 'Max 160 characters').optional().or(z.literal('')),
  readingTimeMinutes: z.number().min(1, 'Min 1 minute').optional(),
  views: z.number().min(0).optional(),
  engagementScore: z.number().min(0).max(100, 'Max 100').optional(),
  isFeatured: z.boolean().optional(),
  editorNote: z.string().optional().or(z.literal('')),
});

export type MetadataFormValues = z.infer<typeof metadataSchema>;
