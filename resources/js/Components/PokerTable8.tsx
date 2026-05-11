import { PageProps, User } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';

export default function PokerTable8({user}: {user?: User}) {

    console.log(user);
    

    const [selectedSeats, setSelectedSeats] = useState<number[]>([]);
    const toggleSeat = (seatNumber: number) => {
        if (selectedSeats.includes(seatNumber)) {
            setSelectedSeats(selectedSeats.filter(s => s !== seatNumber));
        } else {
            setSelectedSeats([...selectedSeats, seatNumber]);
        }
    };

    // Позиции для 8 мест строго по кругу (в процентах)
    // Углы: каждые 45 градусов (360/8 = 45)
    // Начинаем с верхней точки (-90°)
    const seatPositions = [
        { top: '0%', left: '50%', label: 'Место 1', angle: -90 },      // верх (12 часов)
        { top: '14.6%', left: '89.3%', label: 'Место 2', angle: -45 },  // верх-право
        { top: '50%', left: '100%', label: 'Место 3', angle: 0 },       // право (3 часа)
        { top: '85.4%', left: '89.3%', label: 'Место 4', angle: 45 },   // низ-право
        { top: '100%', left: '50%', label: 'Место 5', angle: 90 },      // низ (6 часов)
        { top: '85.4%', left: '10.7%', label: 'Место 6', angle: 135 },  // низ-лево
        { top: '50%', left: '0%', label: 'Место 7', angle: 180 },       // лево (9 часов)
        { top: '14.6%', left: '10.7%', label: 'Место 8', angle: 225 },  // верх-лево
    ];

    return (
        <>
            {/* Покерный стол на 8 мест */}
            <div className="flex justify-center items-center py-8">
                <div className="relative w-[800px] h-[600px] bg-gradient-to-br from-green-700 to-green-900 rounded-full shadow-2xl border-8 border-amber-800">
                    {/* Зеленое сукно */}
                    <div className="absolute inset-[20px] bg-gradient-to-br from-green-600 to-green-800 rounded-full shadow-inner">
                        {/* Разметка стола */}
                        <div className="absolute inset-[10%] border-2 border-amber-700/30 rounded-full"></div>
                        <div className="absolute inset-[25%] border-2 border-amber-700/20 rounded-full"></div>
                        
                        {/* Центр стола */}
                        <div className="absolute inset-[35%] bg-green-700 rounded-full flex flex-col items-center justify-center shadow-inner">
                            <span className="text-amber-800 font-bold text-xl">♠️ ♥️ ♣️ ♦️</span>
                            <span className="text-white/40 text-xs mt-2">8-MAX</span>
                        </div>
                        {/* Линии для 8-местного стола */}
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
                    {/* 8 мест для игроков строго по краям */}
                    {seatPositions.map((seat, index) => {
                        const isSelected = selectedSeats.includes(index + 1);
                        return (
                            <button
                                key={index}
                                onClick={() => toggleSeat(index + 1)}
                                className="absolute transform -translate-x-1/2 -translate-y-1/2 transition-all duration-200 hover:scale-110 focus:outline-none z-10"
                                style={{ top: seat.top, left: seat.left }}
                            >
                                <div className={`
                                    w-14 h-14 rounded-full flex flex-col items-center justify-center
                                    shadow-lg transition-all cursor-pointer
                                    border-2 ${isSelected ? 'border-yellow-400' : 'border-amber-600'}
                                    ${isSelected 
                                        ? 'bg-green-500 ring-4 ring-yellow-400/50' 
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
                    {selectedSeats.length > 0 ? (
                        <div className="flex flex-wrap gap-2 justify-center">
                            {selectedSeats.map(seat => (
                                <span key={seat} className="bg-green-500 text-white px-3 py-1 rounded-full text-sm">
                                    Место {seat}
                                </span>
                            ))}
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