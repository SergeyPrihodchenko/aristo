import { OccupiedSeat } from '@/types';
import { TableOption } from '@/types/table';
import { TelegramUser } from '@/types/telegram';
import { useEffect, useState, useCallback, useRef } from 'react';
import axios from 'axios';

const DEFAULT_AVATAR_URL = '/images/default-avatar.svg';

interface SelectedSeat {
    tableName: string;
    seatNumber: number;
}

interface tableSeats {
    [key: number]: { [key: number]: { top: string; left: string; label: number; angle: number } };
}

export default function PokerTable({ 
    user, 
    currentTable, 
    tableOptions, 
    occupiedSeats 
}: { 
    user: TelegramUser | null; 
    currentTable: string; 
    tableOptions: TableOption[]; 
    occupiedSeats: OccupiedSeat[];
}) {    
    const [selectedSeat, setSelectedSeat] = useState<SelectedSeat | null>(null);
    const [showTable, setShowTable] = useState<SelectedSeat>({tableName: currentTable, seatNumber: 8});
    const [occupiedSeatsState, setOccupiedSeatsState] = useState<OccupiedSeat[]>(occupiedSeats);
    
    // Состояния для загрузки
    const [loadingSeat, setLoadingSeat] = useState<string | null>(null);
    const [error, setError] = useState<string | null>(null);
    
    // Ref для предотвращения множественных запросов
    const isProcessingRef = useRef<boolean>(false);

    const tableSeats: tableSeats = {
        8: {
            1: { top: '0%', left: '50%', label: 1, angle: -90 },
            2: { top: '14.6%', left: '89.3%', label: 2, angle: -45 },
            3: { top: '50%', left: '100%', label: 3, angle: 0 },
            4: { top: '85.4%', left: '89.3%', label: 4, angle: 45 },
            5: { top: '100%', left: '50%', label: 5, angle: 90 },
            6: { top: '85.4%', left: '10.7%', label: 6, angle: 135 },
            7: { top: '50%', left: '0%', label: 7, angle: 180 },
            8: { top: '14.6%', left: '10.7%', label: 8, angle: 225 },
        },
        10: {
            1: { top: '0%', left: '50%', label: 1, angle: -90 },
            2: { top: '9%', left: '85%', label: 2, angle: -54 },
            3: { top: '31%', left: '98%', label: 3, angle: -18 },
            4: { top: '69%', left: '98%', label: 4, angle: 18 },
            5: { top: '91%', left: '85%', label: 5, angle: 54 },
            6: { top: '100%', left: '50%', label: 6, angle: 90 },
            7: { top: '91%', left: '15%', label: 7, angle: 126 },
            8: { top: '69%', left: '2%', label: 8, angle: 162 },
            9: { top: '31%', left: '2%', label: 9, angle: 198 },
            10: { top: '9%', left: '15%', label: 10, angle: 234 },
        } 
    };

    // ID Telegram используется в API, DB ID/telegram_id нужны для сравнения в UI.
    const getUserTelegramId = useCallback(() => user?.telegram_id || null, [user]);
    const getCurrentUserInternalId = useCallback(() => user?.id || null, [user]);

    // Проверить, занято ли место другим игроком
    const isOccupiedByOther = useCallback((tableName: string, seatNumber: number) => {
        const currentUserInternalId = getCurrentUserInternalId();
        const currentTelegramId = getUserTelegramId();

        return occupiedSeatsState.some(os => 
            os.tableName === tableName && 
            os.seatNumber === seatNumber &&
            os.userId !== currentUserInternalId &&
            os.telegramId !== currentTelegramId
        );
    }, [occupiedSeatsState, getCurrentUserInternalId, getUserTelegramId]);

    // Проверить, является ли место выбранным текущим пользователем
    const isSelectedByUser = useCallback((tableName: string, seatNumber: number) => {
        return selectedSeat?.tableName === tableName && selectedSeat?.seatNumber === seatNumber;
    }, [selectedSeat]);

    // Освободить место
    const releaseSeat = useCallback(async (tableName: string, seatNumber: number): Promise<boolean> => {
        const userTelegramId = getUserTelegramId();
        if (!userTelegramId) {
            setError('Пользователь не авторизован');
            return false;
        }

        const seatKey = `release-${tableName}-${seatNumber}`;
        setLoadingSeat(seatKey);
        setError(null);
        
        try {
            const response = await axios.post(route('table.release-seat'), {
                tableName: tableName,
                seatNumber: seatNumber,
                tgUserId: userTelegramId,
            });
            
            if (response.data.success) {
                // Обновляем состояние занятых мест
                setOccupiedSeatsState(prev => 
                    prev.filter(os => !(os.tableName === tableName && os.seatNumber === seatNumber))
                );
                
                // Если освобождаем текущее выбранное место
                if (selectedSeat?.tableName === tableName && selectedSeat?.seatNumber === seatNumber) {
                    setSelectedSeat(null);
                }
                return true;
            } else {
                setError(response.data.message || 'Ошибка при освобождении места');
                return false;
            }
        } catch (err) {
            const errorMsg = axios.isAxiosError(err) 
                ? err.response?.data?.message || 'Ошибка сети'
                : 'Произошла ошибка';
            setError(errorMsg);
            console.error('Ошибка при освобождении места:', err);
            return false;
        } finally {
            setLoadingSeat(null);
        }
    }, [getUserTelegramId, selectedSeat]);

    // Забронировать место
    const reserveSeat = useCallback(async (tableName: string, seatNumber: number): Promise<boolean> => {
        const userTelegramId = getUserTelegramId();
        if (!userTelegramId) {
            setError('Пользователь не авторизован');
            return false;
        }

        const seatKey = `reserve-${tableName}-${seatNumber}`;
        setLoadingSeat(seatKey);
        setError(null);
        
        try {
            const response = await axios.post(route('table.reserve-seat'), {
                tableName: tableName,
                seatNumber: seatNumber,
                tgUserId: userTelegramId,
            });
            
            if (response.data.success) {
                const photoUrl = response.data.photoUrl || user?.photo_url || null;
                const newOccupiedSeat: OccupiedSeat = {
                    tableName: tableName,
                    seatNumber: seatNumber,
                    photoUrl: photoUrl,
                    userId: user?.id,
                    telegramId: userTelegramId,
                };
                
                setOccupiedSeatsState(prev => {
                    // Удаляем возможное старое занятое место (если есть)
                    const filtered = prev.filter(os => 
                        !(os.tableName === tableName && os.seatNumber === seatNumber)
                    );
                    return [...filtered, newOccupiedSeat];
                });
                
                setSelectedSeat({ tableName, seatNumber });
                return true;
            } else {
                setError(response.data.message || 'Ошибка при бронировании места');
                return false;
            }
        } catch (err) {
            const errorMsg = axios.isAxiosError(err) 
                ? err.response?.data?.message || 'Ошибка сети'
                : 'Произошла ошибка';
            setError(errorMsg);
            console.error('Ошибка при бронировании места:', err);
            return false;
        } finally {
            setLoadingSeat(null);
        }
    }, [getUserTelegramId, user]);

    // Основная логика переключения места
    const toggleSeat = useCallback(async (seatOption: SelectedSeat) => {
        // Предотвращаем множественные клики
        if (isProcessingRef.current) {
            console.log('Операция уже выполняется, подождите...');
            return;
        }
        
        const isSelected = isSelectedByUser(seatOption.tableName, seatOption.seatNumber);
        const isOccupied = isOccupiedByOther(seatOption.tableName, seatOption.seatNumber);
        
        // Если место уже занято другим игроком - нельзя взаимодействовать
        if (isOccupied) {
            setError('Это место уже занято другим игроком');
            return;
        }
        
        isProcessingRef.current = true;
        
        try {
            if (isSelected) {
                // Снять бронь с текущего места
                await releaseSeat(seatOption.tableName, seatOption.seatNumber);
            } else {
                // Если есть другое забронированное место - освободить его
                if (selectedSeat) {
                    console.log('Освобождаем старое место:', selectedSeat);
                    await releaseSeat(selectedSeat.tableName, selectedSeat.seatNumber);
                }
                
                // Бронируем новое место
                await reserveSeat(seatOption.tableName, seatOption.seatNumber);
            }
        } catch (error) {
            console.error('Ошибка в toggleSeat:', error);
            setError('Произошла ошибка. Попробуйте снова.');
        } finally {
            isProcessingRef.current = false;
        }
    }, [isSelectedByUser, isOccupiedByOther, releaseSeat, reserveSeat, selectedSeat]);

    // Снять бронь (для отображения в UI)
    const handleReleaseCurrentSeat = useCallback(async () => {
        if (selectedSeat) {
            await releaseSeat(selectedSeat.tableName, selectedSeat.seatNumber);
        }
    }, [selectedSeat, releaseSeat]);

    // Обновление отображаемого стола
    useEffect(() => {
        const currentTableOption = tableOptions.find(option => option.name === currentTable);
        if (currentTableOption) {
            setShowTable({ 
                tableName: currentTableOption.name, 
                seatNumber: currentTableOption.seats 
            });
        }
    }, [currentTable, tableOptions]);

    useEffect(() => {
        setOccupiedSeatsState(occupiedSeats);
    }, [occupiedSeats]);

    useEffect(() => {
        const currentTelegramId = getUserTelegramId();
        if (!currentTelegramId) {
            setSelectedSeat(null);
            return;
        }

        const userSeat = occupiedSeatsState.find(os => os.telegramId === currentTelegramId);
        setSelectedSeat(userSeat ? { tableName: userSeat.tableName, seatNumber: userSeat.seatNumber } : null);
    }, [occupiedSeatsState, getUserTelegramId]);

    // Генерация ключа для отслеживания загрузки конкретного места
    const getSeatLoadingKey = useCallback((seatNumber: number, action: 'reserve' | 'release') => {
        return `${action}-${showTable.tableName}-${seatNumber}`;
    }, [showTable.tableName]);

    // Автоматическое скрытие ошибки через 3 секунды
    useEffect(() => {
        if (error) {
            const timer = setTimeout(() => setError(null), 3000);
            return () => clearTimeout(timer);
        }
    }, [error]);

    return (
        <>
            <div className="flex justify-center items-center py-8">
                <div className="relative w-[800px] h-[600px] bg-gradient-to-br from-green-700 to-green-900 rounded-full shadow-2xl border-8 border-amber-800">
                    {/* Зеленое сукно */}
                    <div className="absolute inset-[20px] bg-gradient-to-br from-green-600 to-green-800 rounded-full shadow-inner" style={{ backgroundImage: 'url(https://static.independent.co.uk/2024/11/13/12/how-to-play-poker-copy.jpg)', backgroundSize: 'cover', backgroundPosition: 'center' }}>
                        {/* Разметка стола */}
                        <div className="absolute inset-[10%] border-2 border-amber-700/30 rounded-full"></div>
                        <div className="absolute inset-[25%] border-2 border-amber-700/20 rounded-full"></div>
                        
                        {/* Центр стола */}
                        <div className="absolute inset-[35%] bg-green-700 rounded-full flex flex-col items-center justify-center shadow-inner">
                            <span className="text-amber-800 font-bold text-xl text-center">♠️ ♥️</span>
                            <span className="text-white/40 text-xs mt-2 text-center">POKER ARISTOKRAT</span>
                        </div>
                        <div className="absolute inset-[15%]">
                            <div className="absolute top-1/2 left-0 right-0 h-0.5 bg-amber-700/20 transform -translate-y-1/2"></div>
                            <div className="absolute left-1/2 top-0 bottom-0 w-0.5 bg-amber-700/20 transform -translate-x-1/2"></div>
                            <div className="absolute top-0 left-0 right-0 bottom-0">
                                <div className="absolute top-0 left-1/2 w-0.5 h-1/2 bg-amber-700/20 transform -translate-x-1/2 rotate-45 origin-bottom"></div>
                                <div className="absolute top-1/2 left-0 w-1/2 h-0.5 bg-amber-700/20 transform -translate-y-1/2 rotate-45 origin-right"></div>
                                <div className="absolute bottom-0 left-1/2 w-0.5 h-1/2 bg-amber-700/20 transform -translate-x-1/2 -rotate-45 origin-top"></div>
                                <div className="absolute top-1/2 right-0 w-1/2 h-0.5 bg-amber-700/20 transform -translate-y-1/2 -rotate-45 origin-left"></div>
                            </div>
                        </div>
                    </div>
                    
                    {Object.values(tableSeats[showTable.seatNumber]).map((seat, index) => {
                        const isSelected = isSelectedByUser(showTable.tableName, seat.label);
                        const isOccupiedByOtherPlayer = isOccupiedByOther(showTable.tableName, seat.label);
                        const isLoading = loadingSeat === getSeatLoadingKey(seat.label, 'reserve') ||
                                        loadingSeat === getSeatLoadingKey(seat.label, 'release');
                        
                        // Занято другим игроком
                        if (isOccupiedByOtherPlayer) {
                            const occupiedSeat = occupiedSeatsState.find(
                                os => os.tableName === showTable.tableName && 
                                      os.seatNumber === seat.label &&
                                      os.telegramId !== getUserTelegramId()
                            );
                            
                            return (
                                <div
                                    key={index}
                                    className="absolute -translate-x-1/2 -translate-y-1/2 z-10 cursor-not-allowed"
                                    style={{
                                        top: seat.top,
                                        left: seat.left,
                                    }}
                                    title="Занято другим игроком"
                                >
                                    <div
                                        className="
                                            w-14 h-14 rounded-full overflow-hidden
                                            border-2 border-red-500 bg-red-700
                                            flex items-center justify-center
                                            shadow-lg opacity-90
                                        "
                                    >
                                        <img
                                            src={occupiedSeat?.photoUrl || DEFAULT_AVATAR_URL}
                                            alt="Player"
                                            className="w-full h-full object-cover"
                                            onError={(event) => {
                                                event.currentTarget.src = DEFAULT_AVATAR_URL;
                                            }}
                                        />
                                    </div>
                                </div>
                            );
                        }
                        
                        // Кнопка для свободного или выбранного места
                        return (
                            <button
                                key={index}
                                onClick={() => !isLoading && toggleSeat({
                                    tableName: showTable.tableName,
                                    seatNumber: seat.label,
                                })}
                                disabled={isLoading}
                                className="absolute -translate-x-1/2 -translate-y-1/2 transition-all duration-200 hover:scale-110 focus:outline-none z-10 disabled:opacity-50 disabled:cursor-not-allowed"
                                style={{
                                    top: seat.top,
                                    left: seat.left,
                                }}
                            >
                                <div
                                    className={`
                                        relative w-14 h-14 rounded-full overflow-hidden
                                        shadow-lg transition-all
                                        border-2
                                        ${isLoading ? 'animate-pulse' : ''}
                                        ${
                                            isSelected
                                                ? 'border-yellow-400 ring-2 ring-yellow-400/50'
                                                : 'border-amber-600 bg-amber-700 hover:bg-amber-600'
                                        }
                                    `}
                                >
                                    {isLoading ? (
                                        <div className="absolute inset-0 bg-black/50 flex items-center justify-center">
                                            <div className="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                        </div>
                                    ) : isSelected ? (
                                        <>
                                            <img
                                                src={user?.photo_url || DEFAULT_AVATAR_URL}
                                                alt="You"
                                                className="absolute inset-0 w-full h-full object-cover"
                                                onError={(event) => {
                                                    event.currentTarget.src = DEFAULT_AVATAR_URL;
                                                }}
                                            />
                                            <div className="absolute inset-0 bg-black/30 flex items-center justify-center">
                                                <span className="text-white font-bold text-xl">
                                                    ✓
                                                </span>
                                            </div>
                                        </>
                                    ) : (
                                        <div className="w-full h-full flex flex-col items-center justify-center">
                                            <div className="text-white text-[10px] font-bold">
                                                {seat.label}
                                            </div>
                                            <div className="text-white text-lg">
                                                💺
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </button>
                        );
                    })}
                    
                    {/* Декоративные карточные символы */}
                    <div className="absolute top-8 left-8 text-amber-600/30 text-3xl">♠️</div>
                    <div className="absolute top-8 right-8 text-amber-600/30 text-3xl">♥️</div>
                    <div className="absolute bottom-8 left-8 text-amber-600/30 text-3xl">♣️</div>
                    <div className="absolute bottom-8 right-8 text-amber-600/30 text-3xl">♦️</div>
                </div>
            </div>
            
            {/* Информация о бронировании и ошибках */}
            <div className="mt-8 text-center">
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 max-w-md mx-auto">
                    <h3 className="text-lg font-semibold mb-2">Забронированные места:</h3>
                    {selectedSeat !== null ? (
                        <div className="flex flex-wrap gap-2 justify-center items-center">
                            <span className="bg-green-500 text-white px-3 py-1 rounded-full text-sm">
                                Место {selectedSeat.seatNumber} на столе {selectedSeat.tableName}
                            </span>
                            <button
                                onClick={handleReleaseCurrentSeat}
                                disabled={!!loadingSeat}
                                className="bg-red-500 text-white px-3 py-1 rounded-full text-sm hover:bg-red-600 transition-colors disabled:opacity-50"
                            >
                                {loadingSeat?.startsWith('release') ? 'Освобождение...' : 'Освободить'}
                            </button>
                        </div>
                    ) : (
                        <p className="text-gray-500">Нет забронированных мест</p>
                    )}
                    
                    {/* Отображение ошибок */}
                    {error && (
                        <div className="mt-3 p-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg text-sm animate-pulse">
                            ⚠️ {error}
                        </div>
                    )}
                    
                    {/* Индикатор общей загрузки */}
                    {loadingSeat && !error && (
                        <div className="mt-3 text-sm text-gray-500 flex items-center justify-center gap-2">
                            <div className="w-4 h-4 border-2 border-gray-500 border-t-transparent rounded-full animate-spin"></div>
                            <span>Выполняется операция...</span>
                        </div>
                    )}
                    
                    <p className="text-sm text-gray-500 mt-3">
                        💡 Нажмите на место для бронирования | {showTable.seatNumber}-max table
                    </p>
                </div>
            </div>
        </>
    );
}