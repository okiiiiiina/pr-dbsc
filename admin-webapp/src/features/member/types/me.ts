import { WorkspaceForMe } from '@/features/workspace/types/workspaceForMe'

export type Me = {
  userID: string
  name: string
  email: string
  logoPath: string
  role: string
  updatedAt: string
  workspace?: WorkspaceForMe
}
