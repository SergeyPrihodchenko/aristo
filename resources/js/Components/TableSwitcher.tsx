// resources/js/Components/TableSwitcher.tsx
import { useState } from 'react';

interface TableSwitcherProps {
    onTableChange: (tableType: '8max' | '10max') => void;
    currentTable: '8max' | '10max';
}

export default function TableSwitcher({ onTableChange, currentTable }: TableSwitcherProps) {
    return (
        <div className="flex justify-center items-center gap-4 mb-6">
            <button
                onClick={() => onTableChange('8max')}
                className={`
                    relative px-6 py-2 rounded-lg font-semibold transition-all duration-300
                    ${currentTable === '8max' 
                        ? 'bg-amber-600 text-white shadow-lg scale-105 ring-2 ring-amber-400' 
                        : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:scale-105'
                    }
                `}
            >
                <span className="flex items-center gap-1">
                    <span>♥️</span>
                    8 мест
                    <span className="text-sm bg-gray-700 text-gray-300 rounded">свободно 6</span>
                </span>
            </button>
            
            <button
                onClick={() => onTableChange('10max')}
                className={`
                    relative px-6 py-2 rounded-lg font-semibold transition-all duration-300
                    ${currentTable === '10max' 
                        ? 'bg-amber-600 text-white shadow-lg scale-105 ring-2 ring-amber-400' 
                        : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:scale-105'
                    }
                `}
            >
                <span className="flex items-center gap-1">
                    <span>♠️</span>
                    10 мест
                    <span className="text-sm bg-gray-700 text-gray-300 rounded">свободно 8</span>
                </span>
            </button>
        </div>
    );
}