/**
 * Session Manager
 */
export class SessionManager {
  /**
   * Initialize logout click handler
   * 
   * Use this method to handle logout click events for elements
   * with the class `.js-action-logout-click`. Multiple elements
   * can trigger this event.
   */
  public static initLogoutClick(): void {
      const logoutLinks = document.querySelectorAll('.js-action-logout-click') as NodeListOf<HTMLElement>;
      logoutLinks.forEach((logoutLink) => { 
          logoutLink.addEventListener('click', SessionManager.handleLogoutClick);
      });
  }

  /**
   * Handle logout click
   */
  private static handleLogoutClick(): void {
    sessionStorage.setItem("user_logged_out", "true");
  }
}

/**
* Initialize the session manager
*/
export function initializeSessionManager(): void {
  document.addEventListener('DOMContentLoaded', () => {
      SessionManager.initLogoutClick();
  });
}