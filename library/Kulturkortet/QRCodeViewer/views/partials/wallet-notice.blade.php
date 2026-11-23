@if ($walletError !== null)
    <p role="alert" lang="sv" data-kulturkortet-wallet-notice="{{ \Municipio\Kulturkortet\Wallet\WalletService::APPLE_PASS_EXPIRED_QUERY_VAR }}">
        {{ $walletError }}
    </p>
@endif
