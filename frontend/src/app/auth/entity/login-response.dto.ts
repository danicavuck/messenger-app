import {User} from "./user.entity";
import {AuthTokens} from "./tokens.dto";

export interface LoginResponseDto extends AuthTokens{
  user: User,
}
