export type ProgressBarUpdate = {
    label: string | null;
    value: number | null;
}

export interface IProgressBar {
    update(event: ProgressBarUpdate): void;
    show(): void;
    hide(): void;
}
