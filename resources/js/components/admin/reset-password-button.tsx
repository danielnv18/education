import { Button } from '@/components/ui/button';
import { User } from '@/types';
import { router } from '@inertiajs/react';
import { KeyIcon } from 'lucide-react';
import { useState } from 'react';

interface ResetPasswordButtonProps {
    user: User;
}

export default function ResetPasswordButton({ user }: ResetPasswordButtonProps) {
    const [isSending, setIsSending] = useState(false);

    const handleResetPassword = () => {
        setIsSending(true);
        router.post(
            route('admin.users.send-password-reset-link', user.id),
            {},
            {
                onSuccess: () => {
                    setIsSending(false);
                },
                onError: () => {
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
