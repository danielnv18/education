import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

export interface UserFormData {
    name: string;
    email: string;
    roles: string[];
    avatar: string | null;
}

export interface UserFormProps {
    initialData?: Partial<UserFormData>;
    availableRoles: App.Data.RoleData[];
    onSubmit: (data: UserFormData) => void;
    submitLabel?: string;
    processing?: boolean;
    errors?: Partial<Record<keyof UserFormData, string>>;
}

export default function UserForm({
    initialData,
    availableRoles,
    onSubmit,
    submitLabel = 'Save',
    processing = false,
    errors = {},
}: UserFormProps) {
    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        const formData = new FormData(e.currentTarget);
        const name = formData.get('name') as string;
        const email = formData.get('email') as string;
        const avatar = (formData.get('avatar') as string) || null;
        const roles = formData.getAll('roles') as string[];

        onSubmit({
            name,
            email,
            avatar,
            roles,
        });
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            <div className="grid gap-2">
                <Label htmlFor="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    type="text"
                    defaultValue={initialData?.name ?? ''}
                    placeholder="Enter name"
                    required
                    aria-invalid={!!errors.name}
                />
                <InputError message={errors.name} />
            </div>

            <div className="grid gap-2">
                <Label htmlFor="email">Email</Label>
                <Input
                    id="email"
                    name="email"
                    type="email"
                    defaultValue={initialData?.email ?? ''}
                    placeholder="Enter email"
                    required
                    aria-invalid={!!errors.email}
                />
                <InputError message={errors.email} />
            </div>

            <div className="grid gap-2">
                <Label htmlFor="avatar">Avatar URL</Label>
                <Input
                    id="avatar"
                    name="avatar"
                    type="url"
                    defaultValue={initialData?.avatar ?? ''}
                    placeholder="https://example.com/avatar.jpg"
                    aria-invalid={!!errors.avatar}
                />
                <InputError message={errors.avatar} />
            </div>

            <div className="grid gap-2">
                <Label>Roles</Label>
                <div className="flex flex-wrap gap-4">
                    {availableRoles.map((role) => (
                        <div
                            key={role.name}
                            className="flex items-center gap-2"
                        >
                            <Checkbox
                                id={`role-${role.name}`}
                                name="roles"
                                value={role.name}
                                defaultChecked={initialData?.roles?.includes(
                                    role.name,
                                )}
                            />
                            <Label
                                htmlFor={`role-${role.name}`}
                                className="cursor-pointer font-normal"
                            >
                                {role.name}
                            </Label>
                        </div>
                    ))}
                </div>
                <InputError message={errors.roles} />
            </div>

            <Button type="submit" disabled={processing}>
                {processing ? 'Saving...' : submitLabel}
            </Button>
        </form>
    );
}
