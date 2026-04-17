// src/content.config.ts
import { z, defineCollection } from 'astro:content';
import { glob } from 'astro/loaders';

const blogCollection = defineCollection({
    // Reads all .md files inside src/content/blog/es/ and src/content/blog/en/
    // post.id will be "es/mi-primer-post" or "en/where-to-play-pickleball"
    loader: glob({ pattern: "**/*.{md,mdx}", base: "./src/content/blog" }),

    schema: z.object({
        title: z.string(),
        description: z.string(),
        pubDate: z.date(),
        image: z.string().optional(),
        category: z.enum([
            'Torneos de Pickleball', 'Oferta', 'Info', 'Clases',   // ES
            'Pickleball Tournaments', 'Offer', 'Classes',            // EN
        ]),
        draft: z.boolean().default(false),
    }),
});

export const collections = {
    'blog': blogCollection,
};