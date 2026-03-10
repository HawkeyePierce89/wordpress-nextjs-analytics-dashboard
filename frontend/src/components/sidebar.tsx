'use client';

import { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { LayoutDashboard, FileText, BarChart3, Menu, X } from 'lucide-react';

const navLinks = [
  { href: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
  { href: '/posts', label: 'Posts', icon: FileText },
  { href: '/analytics', label: 'Analytics', icon: BarChart3 },
];

export function Sidebar() {
  const pathname = usePathname();
  const [isOpen, setIsOpen] = useState(false);

  return (
    <>
      {/* Mobile hamburger button */}
      <button
        onClick={() => setIsOpen(true)}
        className="fixed top-3 left-3 z-50 md:hidden p-2 rounded-md bg-gray-800 text-gray-300 hover:text-white hover:bg-gray-700 transition-colors"
        aria-label="Open menu"
      >
        <Menu size={20} />
      </button>

      {/* Mobile backdrop */}
      {isOpen && (
        <div
          className="fixed inset-0 z-40 bg-black/50 md:hidden"
          onClick={() => setIsOpen(false)}
        />
      )}

      {/* Sidebar */}
      <aside
        className={`fixed left-0 top-0 h-screen w-[220px] bg-gray-950 border-r border-gray-800 flex flex-col z-50 transition-transform duration-200 ${
          isOpen ? 'translate-x-0' : '-translate-x-full'
        } md:translate-x-0`}
      >
        {/* Logo + close button */}
        <div className="px-4 py-5 border-b border-gray-800 flex items-center justify-between">
          <div>
            <p className="text-white font-semibold text-sm leading-tight">
              Content Analytics
            </p>
            <p className="text-gray-500 text-xs mt-0.5">WordPress Dashboard</p>
          </div>
          <button
            onClick={() => setIsOpen(false)}
            className="md:hidden p-1 rounded text-gray-400 hover:text-white transition-colors"
            aria-label="Close menu"
          >
            <X size={18} />
          </button>
        </div>

        {/* Nav */}
        <nav className="flex-1 py-4 px-2 space-y-0.5">
          {navLinks.map(({ href, label, icon: Icon }) => {
            const isActive = pathname === href || pathname.startsWith(`${href}/`);
            return (
              <Link
                key={href}
                href={href}
                onClick={() => setIsOpen(false)}
                className={`flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-colors relative ${
                  isActive
                    ? 'text-blue-400 bg-blue-400/5 border-l-2 border-blue-400 pl-[10px]'
                    : 'text-gray-400 hover:text-gray-100 hover:bg-gray-800'
                }`}
              >
                <Icon size={16} />
                {label}
              </Link>
            );
          })}
        </nav>
      </aside>
    </>
  );
}
