import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { User } from '@/types';
import { useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

interface UserFormProps {
    user?: User;
    action: string;
}

export default function UserForm({ user, action }: UserFormProps) {
    const { data, setData, post, put, processing, errors } = useForm({
        name: user?.name || '',
        email: user?.email || '',
        password: '',
        password_confirmation: '',
        roles: user?.roles.map((r) => r.name) || [],
        email_verified: user ? !!user.email_verified_at : false,
    });

    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault();

        if (user) {
            put(action);
        } else {
            post(action);
        }
    };

    const toggleRole = (role: string) => {
        const roles = [...data.roles];

        const hasRole = roles.some((r) => (typeof r === 'object' ? r === role : r === role));

        if (hasRole) {
            setData(
                'roles',
                roles.filter((r) => (typeof r === 'object' ? r !== role : r !== role)),
            );
        } else {
            setData('roles', [...roles, role]);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-4">
                <div>
                    <Label htmlFor="name">Name</Label>
                    <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} />
                    <InputError message={errors.name} />
                </div>

                <div>
                    <Label htmlFor="email">Email</Label>
                    <Input id="email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} />
                    <InputError message={errors.email} />
                </div>

                <div>
                    <Label htmlFor="password">Password {user ? '(leave blank to keep current password)' : '(optional)'}</Label>
                    <Input id="password" type="password" value={data.password} onChange={(e) => setData('password', e.target.value)} />
                    <InputError message={errors.password} />
                    {!user && (
                        <p className="mt-1 text-sm text-gray-500">
                            If left blank, a default password will be set and the user will need to reset it.
                        </p>
                    )}
                </div>

                <div>
                    <Label htmlFor="password_confirmation">Confirm Password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                    />
                </div>

                <div>
                    <Label>Roles (Optional)</Label>
                    <div className="mt-2 space-y-2">
                        <div className="flex items-center space-x-2">
                            <Checkbox
                                id="role-admin"
                                checked={data.roles.some((r) => (typeof r === 'object' ? r === 'admin' : r === 'admin'))}
                                onCheckedChange={() => toggleRole('admin')}
                            />
                            <Label htmlFor="role-admin">Admin</Label>
                        </div>
                        <div className="flex items-center space-x-2">
                            <Checkbox
                                id="role-teacher"
                                checked={data.roles.some((r) => (typeof r === 'object' ? r === 'teacher' : r === 'teacher'))}
                                onCheckedChange={() => toggleRole('teacher')}
                            />
                            <Label htmlFor="role-teacher">Teacher</Label>
                        </div>
                    </div>
                    <InputError message={errors.roles} />
                </div>

                <div>
                    <Label>Email Verification</Label>
                    <div className="mt-2 space-y-2">
                        <div className="flex items-center space-x-2">
                            <Checkbox
                                id="email-verified"
                                checked={data.email_verified}
                                onCheckedChange={(checked) => setData('email_verified', !!checked)}
                            />
                            <Label htmlFor="email-verified">Mark as verified</Label>
                        </div>
                    </div>
                    <InputError message={errors.email_verified} />
                </div>
            </div>

            <Button type="submit" disabled={processing}>
                {user ? 'Update User' : 'Create User'}
            </Button>
        </form>
    );
}
