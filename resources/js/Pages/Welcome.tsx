import TableSwitcher from '@/Components/TableSwitcher';
import { PageProps } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';
import { TelegramUser } from '@/types/telegram';
import PokerTable from '@/Components/PokerTable';
import { TableOption } from '@/types/table';

export default function Welcome({
    auth,
    tableOptions,
}: PageProps<{ tableOptions: TableOption[]}>) {
    
    const [tgUser, setTgUser] = useState<TelegramUser | null>(null);
    const [isTgWebApp, setIsTgWebApp] = useState(false);
    const [tableOptionsState, setTableOptionsState] = useState<TableOption[]>(tableOptions);

    const [errorVisible, setErrorVisible] = useState('');

    // useEffect(() => {
    //     const tg = (window as any).Telegram.WebApp;
    //     if (!tg) {
    //         console.error('Telegram WebApp API is not available.');
    //         return;
    //     }
    //     setIsTgWebApp(true);
    //     tg.ready();

    //     const user = tg.initDataUnsafe.user;

    //     setTgUser({
    //         telegram_id: user.id,
    //         first_name: user.first_name,
    //         last_name: user.last_name,
    //         username: user.username,
    //         language_code: user.language_code,
    //         is_premium: user.is_premium,
    //     });

    //     axios.post(route('telegram.create-user'), {
    //         telegram_id: user.id,
    //         first_name: user.first_name,
    //         last_name: user.last_name,
    //         username: user.username,
    //         language_code: user.language_code,
    //         is_premium: user.is_premium,
    //     }).then(response => {
    //         setTgUser(prev => prev ? { ...prev, photo_url: response.data.user.photo_url } : null);
    //     }).catch(error => {
    //         console.error('Error creating user in Telegram:', error);
    //             setErrorVisible(error.response?.data?.message || error.message || error.toString());
    //     });

    //     if(!tgUser?.photo_url) {
    //         setTimeout(() => {
    //             axios.post(route('telegram.get-avatar', { telegram_id: user.id }))
    //             .then(response => {
    //                 setTgUser(prev => prev ? { ...prev, photo_url: response.data.photo_url } : null);
    //             });
    //         }, 4000);
    //     }
    // }, []);
        
    return (
        <>
            <Head title="Welcome" />
            <div className="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
                <div className="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                    <div className="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                        <header className="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                            <div className="flex flex-1 justify-start">
                                <h1 className="text-2xl font-bold tracking-tight text-black dark:text-white">
                                    Привет, {tgUser ? tgUser.first_name : 'Гость'}!
                                </h1>
                                <span className="ml-2 text-sm text-black/50 dark:text-white/50">
                                    {errorVisible && `Ошибка: ${errorVisible}`}
                                </span>
                                {tgUser && tgUser.photo_url && (
                                    <img src={tgUser.photo_url} alt="Avatar" className="ml-2 h-10 w-10 rounded-full" />
                                )}
                            </div>
                            {/* <nav className="-mx-3 flex flex-1 justify-end gap-2">
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
                            </nav> */}
                        </header>
                        <main className="mt-6">
                            <TableSwitcher tableOptions={tableOptionsState} />
                                <PokerTable user={tgUser} />
                        </main>
                        <footer className="py-16 text-center text-sm text-black dark:text-white/70">
                        </footer>
                    </div>
                </div>
            </div>
        </>
    );
}