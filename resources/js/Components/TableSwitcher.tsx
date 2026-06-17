import { TableOption, TableSwitcher as TableSwitcherInerface } from '@/types/table';
import { OccupiedSeat } from '@/types';

export default function TableSwitcher({ tableOptions, handleTableChange, currentTable, occupiedSeats }: { tableOptions: TableOption[], handleTableChange: (tableType: string) => void, currentTable: string, occupiedSeats: OccupiedSeat[] }) {

    const tablesSwitchers: TableSwitcherInerface[] = tableOptions.map(option => ({
        option,
        currentTable,
        onTableChange: handleTableChange,
    }));

    return (
        <div className="mb-6 overflow-x-auto hide-scrollbar">
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
                                    свободно {Math.max(option.seats - occupiedSeats.filter((os) => os.tableName === option.name).length, 0)}
                                </span>
                            </span>
                        </button>
                    )
                )}
            </div>
        </div>
    );
}