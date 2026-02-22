import { slugify } from '@/lib/utils';

/**
 * Base URL for team logos in the public folder.
 * Place images at: public/assets/images/teams/{slug}.png
 * e.g. public/assets/images/teams/arsenal.png for "Arsenal"
 */
const TEAM_LOGO_BASE = '/assets/images/teams';

/**
 * Returns the public URL for a team's logo by name.
 * Use in img src; add @error on the img to handle missing images.
 *
 * @example
 * <img :src="getTeamLogoUrl(team.name)" :alt="team.name" @error="hideImg" />
 */
export function getTeamLogoUrl(teamName: string): string {
  return `${TEAM_LOGO_BASE}/${slugify(teamName)}.png`;
}
