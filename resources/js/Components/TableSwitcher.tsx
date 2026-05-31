import { TableOption, TableSwitcher as TableSwitcherInerface } from '@/types/table';
import { useEffect, useState } from 'react';

export default function TableSwitcher({ tableOptions, handleTableChange, currentTable }: { tableOptions: TableOption[], handleTableChange: (tableType: string) => void, currentTable: string }) {

    const [tablesSwitchers, setTablesSwitchers] = useState<TableSwitcherInerface[]>([]);
    const initializeTableSwitchers = () => {
        const switchers = tableOptions.map(option => ({
            option,
            currentTable,
            onTableChange: handleTableChange,
        }));
        setTablesSwitchers(switchers);
    }

    useEffect(() => {
        initializeTableSwitchers();
    }, [tableOptions, currentTable]);

    return (
        <div className="mb-6 overflow-x-auto">
            <div className="flex justify-start items-center gap-4 min-w-max px-1">
                {tablesSwitchers.map(
                    ({ currentTable, onTableChange, option }: TableSwitcherInerface, index) => (
                        <button
                            key={index}
                            onClick={() => onTableChange(option.name)}
                            className={`
                                relative shrink-0 px-6 py-2 rounded-lg font-semibold transition-all duration-300
                                ${
                                    currentTable === option.name
                                        ? 'bg-amber-600 text-white shadow-lg scale-105 ring-2 ring-amber-400'
                                        : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:scale-105'
                                }
                            `}
                        >
                            <span className="flex items-center gap-1 whitespace-nowrap">
                                <span>♥️</span>
                                {option.seats} мест
                                <span className="text-sm bg-gray-700 text-gray-300 rounded px-2">
                                    свободно 6
                                </span>
                            </span>
                        </button>
                    )
                )}
            </div>
        </div>
    );
}