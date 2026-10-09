declare const wpApiSettings: {
  nonce?: string
  nonceRefreshCacheKey?: string
  root?: string
  refreshNonce?: (nonce?: string) => Promise<string | null>
}
declare const wp: { customize?: any }
declare const MunicipioLocale: {
  a11yWarnings?: {
    button?: string
    link?: string
    vagueLabels?: string[]
  }
}

// allow raw-loader to work
declare module '*.css?raw' {
  const content: string
  export default content
}
