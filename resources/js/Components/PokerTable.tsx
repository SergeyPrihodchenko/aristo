import { OccupiedSeat, PageProps, User } from '@/types';
import { TableOption } from '@/types/table';
import { TelegramUser } from '@/types/telegram';
import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';

interface SelectedSeat {
    tableName: string;
    seatNumber: number;
}

interface tableSeats {
    [key: number]: { [key: number]: { top: string; left: string; label: number; angle: number } };
}


export default function PokerTable({user, currentTable, tableOptions, occupiedSeats}: {user: TelegramUser | null, currentTable: string, tableOptions: TableOption[], occupiedSeats: OccupiedSeat[]}) {    

    const [selectedSeat, setSelectedSeat] = useState<SelectedSeat | null>(null);
    const [tableOptionsState, setTableOptionsState] = useState(tableOptions);
    const [showTable, setShowTable] = useState<SelectedSeat>({tableName: currentTable, seatNumber: 8});
    const [occupiedSeatsState, setOccupiedSeatsState] = useState<OccupiedSeat[]>(occupiedSeats); // { tableName: seatNumbers }

    const tableSeats: tableSeats = {
        8: {
            1: { top: '0%', left: '50%', label: 1, angle: -90 },      // верх (12 часов)
            2: { top: '14.6%', left: '89.3%', label: 2, angle: -45 },  // верх-право
            3: { top: '50%', left: '100%', label: 3, angle: 0 },       // право (3 часа)
            4: { top: '85.4%', left: '89.3%', label: 4, angle: 45 },   // низ-право
            5: { top: '100%', left: '50%', label: 5, angle: 90 },      // низ (6 часов)
            6: { top: '85.4%', left: '10.7%', label: 6, angle: 135 },  // низ-лево
            7: { top: '50%', left: '0%', label: 7, angle: 180 },       // лево (9 часов)
            8: { top: '14.6%', left: '10.7%', label: 8, angle: 225 },  // верх-лево
        },
        10: {
            1: { top: '0%', left: '50%', label: 1, angle: -90 },      // верх
            2: { top: '9%', left: '85%', label: 2, angle: -54 },       // верх-право
            3: { top: '31%', left: '98%', label: 3, angle: -18 },      // право-верх
            4: { top: '69%', left: '98%', label: 4, angle: 18 },       // право-низ
            5: { top: '91%', left: '85%', label: 5, angle: 54 },       // низ-право
            6: { top: '100%', left: '50%', label: 6, angle: 90 },      // низ
            7: { top: '91%', left: '15%', label: 7, angle: 126 },      // низ-лево
            8: { top: '69%', left: '2%', label: 8, angle: 162 },       // лево-низ
            9: { top: '31%', left: '2%', label: 9, angle: 198 },       // лево-верх
            10: { top: '9%', left: '15%', label: 10, angle: 234 },      // верх-лево
        } 
    }

    const toggleSeat = (seatOption: SelectedSeat) => {
        if (selectedSeat && selectedSeat.tableName === seatOption.tableName && selectedSeat.seatNumber === seatOption.seatNumber) {
            axios.post(route('table.release-seat'), {
                tableName: seatOption.tableName,
                seatNumber: seatOption.seatNumber,
                tgUserId: user?.telegram_id,
            }).then(response => {
                if (response.data.success) {
                    const game = response.data.game;
                    if(game) {
                        const occupiedSeatIndex = occupiedSeatsState.findIndex(os => os.tableName === seatOption.tableName && os.seatNumber === seatOption.seatNumber);
                        if(occupiedSeatIndex !== -1) {
                            setOccupiedSeatsState(prev => {
                                const newOccupiedSeats = [...prev];
                                newOccupiedSeats.splice(occupiedSeatIndex, 1);
                                return newOccupiedSeats;
                            });
                        }
                    }
                    setOccupiedSeatsState(prev => prev.filter(os => !(os.tableName === seatOption.tableName && os.seatNumber === seatOption.seatNumber)));
                    setSelectedSeat(null); // Снять бронь, если кликнули по уже забронированному месту
                } else {
                    alert('Ошибка при снятии брони с места. Попробуйте снова.');
                }
            }).catch(error => {
                console.error('Ошибка при снятии брони с места:', error);
                alert('Ошибка при снятии брони с места. Попробуйте снова.');
            });
        } else {
            axios.post(route('table.reserve-seat'), {
                tableName: seatOption.tableName,
                seatNumber: seatOption.seatNumber,
                tgUserId: user?.telegram_id,
            }).then(response => {
                if (response.data.success) {
                    const game = response.data.game;
                    const photoUrl = response.data.photoUrl || null;
                    setOccupiedSeatsState(prev => [...prev, { tableName: seatOption.tableName, seatNumber: seatOption.seatNumber, photoUrl }]);
                    setSelectedSeat(seatOption); // Забронировать новое место
                } else {
                    alert('Ошибка при бронировании места. Попробуйте снова.');
                }
            }).catch(error => {
                console.error('Ошибка при бронировании места:', error);
                alert('Ошибка при бронировании места. Попробуйте снова.');
            });
        }
    }

    useEffect(() => {
        tableOptionsState.forEach(option => {
            if(option.name === currentTable) {
                setShowTable({ tableName: option.name, seatNumber: option.seats });
            }
        });
    }, [currentTable]);

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
                        const isSelected = selectedSeat && selectedSeat.tableName === showTable.tableName && selectedSeat.seatNumber === seat.label;
                        const isOccupied = occupiedSeatsState.some(os => os.tableName === showTable.tableName && os.seatNumber === seat.label);
                        if (isOccupied) {
                            const occupiedSeat = occupiedSeatsState.find(os => os.tableName === showTable.tableName && os.seatNumber === seat.label);
                            return (
                                <div
                                    key={index}
                                    className="absolute transform -translate-x-1/2 -translate-y-1/2 z-10"
                                    style={{ top: seat.top, left: seat.left }}
                                >
                                    <div className={`
                                        w-14 h-14 rounded-full flex flex-col items-center justify-center
                                        shadow-lg cursor-not-allowed border-2 border-red-500 bg-red-700
                                        bg-cover bg-center ${occupiedSeat?.photoUrl ? `bg-[url(${occupiedSeat.photoUrl})]` : ''}
                                    `}>
                                    </div>
                                </div>
                            );
                        }
                        return (
                            <button
                                key={index}
                                onClick={() => toggleSeat({ tableName: showTable.tableName, seatNumber: seat.label })}
                                className="absolute transform -translate-x-1/2 -translate-y-1/2 transition-all duration-200 hover:scale-110 focus:outline-none z-10"
                                style={{ top: seat.top, left: seat.left }}
                            >
                                <div className={`
                                    w-14 h-14 rounded-full flex flex-col items-center justify-center
                                    shadow-lg transition-all cursor-pointer
                                    border-2 ${isSelected ? 'border-yellow-400' : 'border-amber-600'}
                                    ${isSelected 
                                        ? user?.photo_url ? `bg-cover bg-center bg-[url(${user.photo_url})]` : 'bg-amber-700' 
                                        : 'bg-amber-700 hover:bg-amber-600'
                                    }
                                `}>
                                    <div className="text-white text-[10px] font-bold">
                                        {index + 1}
                                    </div>
                                    <div className="text-white text-lg">
                                        {isSelected ? '✓' : '💺'}
                                    </div>
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
            {/* Информация о бронировании */}
            <div className="mt-8 text-center">
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 max-w-md mx-auto">
                    <h3 className="text-lg font-semibold mb-2">Забронированные места:</h3>
                    {selectedSeat !== null ? (
                        <div className="flex flex-wrap gap-2 justify-center">
                            <span className="bg-green-500 text-white px-3 py-1 rounded-full text-sm">
                                Место {selectedSeat?.seatNumber} на столе {selectedSeat?.tableName}
                            </span>
                        </div>
                    ) : (
                        <p className="text-gray-500">Нет забронированных мест</p>
                    )}
                    <p className="text-sm text-gray-500 mt-3">
                        💡 Нажмите на место для бронирования | 8-max table
                    </p>
                </div>
            </div>
        </>
    );
}