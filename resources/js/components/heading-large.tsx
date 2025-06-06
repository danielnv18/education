export default function HeadingLarge({ title, description }: { title: string; description?: string }) {
    return (
        <div className="space-y-1">
            <h1 className="text-2xl font-bold tracking-tight">{title}</h1>
            {description && <p className="text-muted-foreground text-sm">{description}</p>}
        </div>
    );
}
