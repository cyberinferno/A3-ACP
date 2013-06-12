USE [ASD]
GO

/****** Object:  Table [dbo].[CharInfo]    Script Date: 06/07/2013 19:54:44 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[CharInfo](
	[AccountID] [char](21) NULL,
	[ServerIdx] [char](2) NULL,
	[CharName] [char](21) NULL,
	[Class] [char](2) NULL,
	[Nation] [char](2) NULL,
	[default] [char](10) NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

