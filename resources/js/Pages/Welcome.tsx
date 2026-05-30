import PokerTable10 from '@/Components/PokerTable10';
import PokerTable8 from '@/Components/PokerTable8';
import TableSwitcher from '@/Components/TableSwitcher';
import { PageProps } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function Welcome({
    auth,
}: PageProps<{}>) {

    const [isDarkMode, setIsDarkMode] = useState(false);
    const [tgUser, setTgUser] = useState<any>(null);
    const [currentTable, setCurrentTable] = useState<'8max' | '10max'>('8max');

    const handleTableChange = (tableType: '8max' | '10max') => {
        setCurrentTable(tableType);
    };

    const tg = (window as any).Telegram.WebApp;

    tg.ready();

    setTimeout(() => {
        const user = tg.initDataUnsafe?.user;
        if (user) {
            setTgUser(user);
        }
    }, 1000);

    console.log(tg.initData);
    console.log(tg.initDataUnsafe);
        
    return (
        <>
            <Head title="Welcome" />
            <div className="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
                <div className="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                    <div className="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                        <header className="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                            <div className="flex flex-1 justify-start">
                                <h1 className="text-2xl font-bold text-black dark:text-white">
                                    Poker Aristocrat
                                </h1>
                            </div>
                            <nav className="-mx-3 flex flex-1 justify-end gap-2">
                                <button
                                    onClick={() => setIsDarkMode(!isDarkMode)}
                                    className="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                >
                                    {isDarkMode ? 'Light Mode' : 'Dark Mode'}
                                </button>
                                {auth.user ? (
                                    <Link
                                        href={route('dashboard')}
                                        className="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                    >
                                        Dashboard
                                    </Link>
                                ) : (
                                    <>
                                        <Link
                                            href={route('login')}
                                            className="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                        >
                                            Log in
                                        </Link>
                                        <Link
                                            href={route('register')}
                                            className="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                        >
                                            Register
                                        </Link>
                                    </>
                                )}
                            </nav>
                        </header>

                        <main className="mt-6">
                            <h1>{tgUser ? `Welcome, ${tgUser.first_name}` : 'Welcome'}</h1>
                            <TableSwitcher currentTable={currentTable} onTableChange={handleTableChange} />
                            {currentTable === '8max' ? (
                                <PokerTable8 user={auth.user} />
                            ) : (
                                <PokerTable10 user={auth.user} />
                            )}
                        </main>
                        <footer className="py-16 text-center text-sm text-black dark:text-white/70">
                        </footer>
                    </div>
                </div>
            </div>
        </>
    );
}