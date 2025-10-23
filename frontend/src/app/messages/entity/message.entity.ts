import {User} from "../../auth/entity/user.entity";

export interface Message {
  id?: string;
  content: string;
  user: User,
  status: string,
  isBot?: boolean,
  created_at: Date,
}
