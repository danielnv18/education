import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';
import { KeyIcon } from 'lucide-react';
import { useState } from 'react';

interface ResetPasswordButtonProps {
    user: App.Data.UserData;
}

export default function ResetPasswordButton({ user }: ResetPasswordButtonProps) {
    const [isSending, setIsSending] = useState(false);

    const handleResetPassword = () => {
        setIsSending(true);
        router.post(
            route('admin.users.send-password-reset-link', { user: user.id }),
            {},
            {
                onFinish: () => {
                    setIsSending(false);
                },
            },
        );
    };

    return (
        <Button variant="outline" onClick={handleResetPassword} disabled={isSending}>
            <KeyIcon className="mr-2 h-4 w-4" />
            {isSending ? 'Sending...' : 'Password Reset'}
        </Button>
    );
}
