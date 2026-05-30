export interface TableSwitcher {
    onTableChange: (tableType: string) => void;
    currentTable: string;
    option: TableOption;
}

export interface TableOption {
    seats: number;
    name: string;
}