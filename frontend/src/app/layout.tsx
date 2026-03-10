import type { Metadata } from 'next';
import { Inter } from 'next/font/google';
import './globals.css';
import { Providers } from './providers';
import { Sidebar } from '@/components/sidebar';

const inter = Inter({ subsets: ['latin'] });

export const metadata: Metadata = {
  title: 'Content Analytics Dashboard',
  description: 'WordPress headless analytics dashboard',
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className="dark">
      <body className={`${inter.className} bg-gray-950 text-gray-100 antialiased`}>
        <Providers>
          <Sidebar />
          <main className="ml-0 md:ml-[220px] min-h-screen p-6 pt-14 md:pt-6">{children}</main>
        </Providers>
      </body>
    </html>
  );
}
