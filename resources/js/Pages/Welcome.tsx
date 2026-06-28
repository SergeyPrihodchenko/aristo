import TableSwitcher from '@/Components/TableSwitcher';
import { OccupiedSeat, PageProps } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';
import { TelegramUser } from '@/types/telegram';
import PokerTable from '@/Components/PokerTable';
import { TableOption } from '@/types/table';

export default function Welcome({
    auth,
    tableOptions,
    occupiedSeats
}: PageProps<{ tableOptions: TableOption[], occupiedSeats: OccupiedSeat[]}>) {
    
    const [tgUser, setTgUser] = useState<TelegramUser | null>(null);
    const [tableOptionsState, setTableOptionsState] = useState<TableOption[]>(tableOptions);
    const [currentTable, setCurrentTable] = useState<string>(tableOptions[0]?.name || '');
    const [adminLink, setAdminLink] = useState<string>('');
    const [isBlocked, setIsBlocked] = useState<boolean>(false);
    const [isAdmin, setIsAdmin] = useState<boolean>(false);
    const handleTableChange = (tableType: string) => {
        setCurrentTable(tableType);
    };

    const redirectToAdminPanel = (
        e: React.MouseEvent<Element, MouseEvent>,
        url: string
    ) => {
        e.preventDefault();

        window.location.href =
            `${url}&tg_user_id=${tgUser?.telegram_id}`;
    };
    
    useEffect(() => {
        const tg = (window as any).Telegram.WebApp;
        if (!tg) {
            axios.post(route('front.error'), {
                error: 'Пользователь телеграмм не получен'
            });
            console.error('Telegram WebApp API is not available.');
            return;
        }
        tg.ready();

        const user = tg.initDataUnsafe.user;

        setTgUser({
            id: 0, // Временный ID, так как в БД будет сгенерирован свой ID
            telegram_id: user.id,
            first_name: user.first_name,
            last_name: user.last_name,
            username: user.username,
            language_code: user.language_code,
            is_premium: user.is_premium,
        });

        axios.post(route('telegram.create-user'), {
            telegram_id: user.id,
            first_name: user.first_name,
            last_name: user.last_name,
            username: user.username,
            language_code: user.language_code,
            is_premium: user.is_premium,
        }).then(response => {
            if (response.data.isBlocked) {
                console.error('User is blocked:', response.data.message);
                setIsBlocked(true);
                return;
            }
            setTgUser(prev => prev ? {
                ...prev,
                id: response.data.user.id,
                photo_url: response.data.user.photo_url,
            } : null);
        }).catch(error => {
            console.error('Error creating user in Telegram:', error);
            axios.post(route('front.error'), {
                error: error
            });
        });

        if(!tgUser?.photo_url) {
            setTimeout(() => {
                axios.post(route('telegram.get-avatar', { telegram_id: user.id }))
                .then(response => {
                    setTgUser(prev => prev ? { ...prev, photo_url: response.data.photo_url } : null);
                });
            }, 4000);
        }

        axios.post(route('get.admin.link'), {
            tg_user_id: user.id
        }).then(response => {
            setIsAdmin(response.data.isAdmin);
            if(response.data.isAdmin) {
                setAdminLink(response.data.adminLink);
            }
        }).catch(error => {
            axios.post(route('front.error'), {
                error: error
            })
        })
        
    }, []);
        
    return (
        <>
            <Head title="Welcome" />
            {isBlocked ? (
                <div className="bg-red-500 text-white p-4 rounded mb-4">
                    Ваш аккаунт заблокирован.
                </div>
            ) : (
                <div className="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
                <div className="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                    <div className="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                        <header className="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                            <div className="flex flex-1 justify-start">
                                <h1 className="text-2xl font-bold tracking-tight text-black dark:text-white">
                                    Привет, {tgUser ? tgUser.first_name : 'Гость'}!
                                </h1>
                                {tgUser && tgUser.photo_url && (
                                    <img src={tgUser.photo_url} alt="Avatar" className="ml-2 h-10 w-10 rounded-full" />
                                )}
                            </div>
                            <nav className="-mx-3 flex flex-1 justify-end gap-2">
                                {/* Красивая кнопка перехода в админпанель, которая будет видна только админам. Она должна вести на страницу админпанели с токеном авторизации в параметрах запроса. */}
                                {isAdmin ? (
                                    <Link
                                        onClick={(e) => redirectToAdminPanel(e, adminLink)}
                                        className="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                    >
                                        Админпанель
                                    </Link>
                                ) : ''}
                            </nav>
                        </header>
                        <main className="mt-6">
                            <TableSwitcher tableOptions={tableOptionsState} handleTableChange={handleTableChange} currentTable={currentTable} occupiedSeats={occupiedSeats}/>
                            <PokerTable user={tgUser} currentTable={currentTable} tableOptions={tableOptions} occupiedSeats={occupiedSeats}/>
                        </main>
                        <footer className="py-16 text-center text-sm text-black dark:text-white/70">
                        </footer>
                    </div>
                </div>
            </div>
            )}
        </>
    );
}